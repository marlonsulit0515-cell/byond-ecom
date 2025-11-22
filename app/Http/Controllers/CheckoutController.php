<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingPrice;
use App\Models\ShippingQuantityRate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class CheckoutController extends Controller
{
    public function calculateShipping(Request $request)
    {
        try {
            $request->validate([
                'province' => 'required|string',
                'delivery_option' => 'required|string|in:ship,pickup'
            ]);

            // Get total quantity from cart
            $cart = session('cart', []);
            
            if (empty($cart)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cart is empty'
                ], 400);
            }

            $totalQuantity = array_sum(array_column($cart, 'quantity'));

            // Use the unified calculation method
            $shippingData = $this->calculateShippingFee(
                $request->province,
                $request->delivery_option,
                $totalQuantity,
                true // Return detailed breakdown
            );

            return response()->json([
                'success' => true,
                'shipping_fee' => $shippingData['fee'],
                'shipping_fee_formatted' => number_format($shippingData['fee'], 2),
                'quantity' => $totalQuantity,
                'province' => $request->province,
                'breakdown' => $shippingData['breakdown']
            ]);

        } catch (\Exception $e) {
            Log::error('Shipping calculation error', [
                'error' => $e->getMessage(),
                'province' => $request->province ?? 'unknown',
                'delivery_option' => $request->delivery_option ?? 'unknown',
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Process checkout and create order
     */
    public function checkout(Request $request)
    {
        // RATE LIMITING: Prevent checkout abuse
        $userId = Auth::id();
        $rateLimitKey = 'checkout:' . $userId;
        
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            $minutes = ceil($seconds / 60);
            
            Log::warning('Checkout rate limit exceeded', [
                'user_id' => $userId,
                'ip' => $request->ip()
            ]);
            
            return redirect()->back()->with('error', 
                "Too many checkout attempts. Please try again in {$minutes} minute(s).");
        }

        // Check if the user has login account before proceeding to checkout
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to complete your order.');
        }

        // Sanitize phone input before validation
        $phoneInput = $request->input('phone', '');
        $sanitizedPhone = $this->sanitizePhone($phoneInput);
        $request->merge(['phone' => $sanitizedPhone]);

        // Enhanced validation with regex for phone
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
            ],
            'country' => 'required|string',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'billing_address' => 'required|string|max:255',
            'delivery_option' => 'required|string|in:ship,pickup',
            'payment_method' => 'required|string|in:paypal',
            'shipping_address' => 'required_if:same_as_billing,false|nullable|string|max:255',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        try {
            // FIXED: N+1 Query Issue - Batch fetch all products first
            $productIds = [];
            foreach ($cart as $cartKey => $item) {
                $productId = $this->extractProductId($cartKey, $item);
                if (!$productId) {
                    return redirect()->route('view-cart')->with('error', 'Invalid product in cart.');
                }
                $productIds[] = $productId;
            }

            // Batch fetch all products in one query
            $products = Product::whereIn('id', $productIds)->get()->keyBy('id');

            // Pre-validate stock availability
            foreach ($cart as $cartKey => $item) {
                $productId = $this->extractProductId($cartKey, $item);
                $product = $products->get($productId);
                
                if (!$product) {
                    return redirect()->route('view-cart')->with('error', "Product not found.");
                }

                $size = $item['size'] ?? 'M';
                $stockField = 'stock_' . strtolower($size);
                $availableStock = $product->$stockField ?? 0;

                if ($item['quantity'] > $availableStock) {
                    return redirect()->route('view-cart')->with('error',
                        "Item '{$product->name}' (Size: {$size}) has insufficient stock. Available: {$availableStock}"
                    );
                }
            }

            DB::beginTransaction();

            $validatedCart = [];
            $total = 0;
            $totalQuantity = 0;

            // FIXED: Use batch-fetched products instead of individual queries
            foreach ($cart as $cartKey => $item) {
                $productId = $this->extractProductId($cartKey, $item);
                $size = $item['size'] ?? 'M';

                // Lock product row to prevent race conditions
                $product = Product::lockForUpdate()->find($productId);
                
                if (!$product) {
                    DB::rollBack();
                    return redirect()->route('view-cart')->with('error', "Product not found.");
                }

                // Final stock check inside transaction
                $stockField = 'stock_' . strtolower($size);
                $availableStock = $product->$stockField ?? 0;

                if ($item['quantity'] > $availableStock) {
                    DB::rollBack();
                    return redirect()->route('view-cart')->with('error',
                        "Item '{$product->name}' (Size: {$size}) stock changed. Available: {$availableStock}"
                    );
                }

                $actualPrice = $product->discount_price ?? $product->price;
                $itemTotal = $actualPrice * $item['quantity'];
                $total += $itemTotal;
                $totalQuantity += $item['quantity'];

                // Store validated cart data
                $validatedCart[] = [
                    'product_id' => $productId,
                    'name' => $product->name,
                    'size' => $size,
                    'quantity' => $item['quantity'],
                    'price' => $actualPrice,
                    'total' => $itemTotal,
                    'image' => basename($product->image),
                ];
            }

            // Validate email
            $userEmail = Auth::user()->email;
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                DB::rollBack();
                Log::error('Invalid user email on checkout', [
                    'user_id' => Auth::id(),
                    'email' => $userEmail
                ]);
                return redirect()->back()->with('error', 'Invalid email address. Please update your profile.');
            }

            // Improved order number with UUID
            $orderNumber = 'ORD-' . strtoupper(Str::random(12)) . '-' . time();

            // Calculate shipping fee using unified method
            $shippingData = $this->calculateShippingFee(
                $request->province,
                $request->delivery_option,
                $totalQuantity
            );
            $shippingFee = $shippingData['fee'];

            // Add shipping fee to total
            $grandTotal = $total + $shippingFee;

            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'email' => $userEmail,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total' => $grandTotal,
                'shipping_fee' => $shippingFee,
                'full_name' => strip_tags($request->full_name),
                'phone' => $request->phone,
                'country' => $request->country,
                'province' => strip_tags($request->province),
                'city' => strip_tags($request->city),
                'barangay' => strip_tags($request->barangay),
                'postal_code' => $request->postal_code,
                'billing_address' => strip_tags($request->billing_address),
                'delivery_option' => $request->delivery_option,
                'same_as_billing' => $request->has('same_as_billing'),
                'shipping_address' => $request->has('same_as_billing')
                    ? strip_tags($request->billing_address)
                    : strip_tags($request->shipping_address),
                'stock_reduced_at' => null, // Initialize for idempotency
            ]);

            Log::info('Checkout initiated', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'subtotal' => $total,
                'shipping_fee' => $shippingFee,
                'total' => $grandTotal,
                'payment_method' => $request->payment_method,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Save Order Items
            foreach ($validatedCart as $item) {
                $order->items()->create([
                    'product_id' => $item['product_id'],
                    'product_name' => $item['name'],
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['total'],
                ]);
            }

            $payment = $order->payment()->create([
                'user_id' => Auth::id(),
                'method' => $request->payment_method,
                'status' => 'pending',
                'amount' => $grandTotal,
            ]);

            // Commit transaction before redirecting to payment gateway
            DB::commit();

            // Hit rate limiter after successful order creation
            RateLimiter::hit($rateLimitKey, 300); // 5 minutes cooldown

            // Store order data securely (will be validated in callback)
            session([
                'order_id' => $order->id,
                'order_hash' => hash_hmac('sha256', $order->id . $order->order_number, config('app.key'))
            ]);

            // Regenerate session for security
            session()->regenerate();

            // Handle payment method (only PayPal now)
            if ($request->payment_method === 'paypal') {
                return $this->createPayPalPayment($order, $request);
            }

            return redirect()->back()->with('error', 'Invalid payment method selected.');

        } catch (\Exception $e) {
            DB::rollBack();
            
            Log::error('Checkout error', [
                'user_id' => Auth::id(),
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ip' => $request->ip()
            ]);
            
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    private function calculateShippingFee($province, $deliveryOption, $totalQuantity, $includeBreakdown = false)
    {
        // If pickup, no shipping fee
        if ($deliveryOption === 'pickup') {
            Log::info('Pickup selected - no shipping fee');
            return [
                'fee' => 0,
                'breakdown' => 'Store pickup selected - no shipping fee'
            ];
        }

        $totalShippingFee = 0;
        $breakdown = [];

        // STEP 1: Get province base rate (REQUIRED)
        $provinceRate = ShippingPrice::where('province', $province)
            ->where('is_active', true)
            ->first();

        if (!$provinceRate) {
            Log::error('Province not found in shipping rates', [
                'province' => $province,
                'quantity' => $totalQuantity
            ]);
            
            throw new \Exception("Shipping is not available for province: {$province}");
        }

        $provincePrice = (float) $provinceRate->price;
        $totalShippingFee = $provincePrice;
        
        if ($includeBreakdown) {
            $breakdown[] = "Province ({$province}): ₱" . number_format($provincePrice, 2);
        }

        Log::info('Province base rate added', [
            'province' => $province,
            'base_rate' => $provinceRate->price,
            'current_total' => $totalShippingFee
        ]);

        // STEP 2: Check for fixed quantity rate and ADD it
        $fixedRate = ShippingQuantityRate::where('is_active', true)
            ->where('quantity_from', '<=', $totalQuantity)
            ->where('quantity_to', '>=', $totalQuantity)
            ->orderBy('quantity_from', 'desc') // More specific ranges first
            ->first();
        
        if ($fixedRate) {
            $quantityFee = (float) $fixedRate->fixed_price;
            $totalShippingFee += $quantityFee;
            
            if ($includeBreakdown) {
                $breakdown[] = "Quantity ({$totalQuantity} items): ₱" . number_format($quantityFee, 2);
            }
            
            Log::info('Fixed quantity rate added to province rate', [
                'province' => $province,
                'province_rate' => $provinceRate->price,
                'quantity' => $totalQuantity,
                'quantity_rate_id' => $fixedRate->id,
                'quantity_range' => "{$fixedRate->quantity_from}-{$fixedRate->quantity_to}",
                'quantity_fee' => $quantityFee,
                'final_total' => $totalShippingFee
            ]);
        } else {
            Log::info('No fixed quantity rate found - using province rate only', [
                'province' => $province,
                'quantity' => $totalQuantity,
                'final_total' => $totalShippingFee
            ]);
        }

        return [
            'fee' => $totalShippingFee,
            'breakdown' => $includeBreakdown ? implode(' + ', $breakdown) : ''
        ];
    }

    private function sanitizePhone($phone)
    {
        // Remove all non-digits except +
        $cleaned = preg_replace('/[^\d+]/', '', $phone);
        
        // Handle Philippine phone format
        if (strlen($cleaned) > 0 && !str_starts_with($cleaned, '63')) {
            if (str_starts_with($cleaned, '0')) {
                $cleaned = '63' . substr($cleaned, 1);
            } else if (str_starts_with($cleaned, '9')) {
                $cleaned = '63' . $cleaned;
            } else if (str_starts_with($cleaned, '+63')) {
                $cleaned = substr($cleaned, 1);
            }
        }
        
        // Remove + if present
        $cleaned = str_replace('+', '', $cleaned);
        
        // Format: +63 XXX XXX XXXX
        if (strlen($cleaned) >= 12) {
            return '+' . substr($cleaned, 0, 2) . ' ' . 
                   substr($cleaned, 2, 3) . ' ' . 
                   substr($cleaned, 5, 3) . ' ' . 
                   substr($cleaned, 8, 4);
        }
        
        return $phone;
    }

    private function extractProductId($cartKey, $item)
    {
        if (isset($item['product_id'])) {
            return $item['product_id'];
        }
        if (is_numeric($cartKey)) {
            return $cartKey;
        }
        $parts = explode('_', $cartKey);
        if (!empty($parts) && is_numeric($parts[0])) {
            return $parts[0];
        }
        return null;
    }

    private function getVerifiedOrder($orderId)
    {
        return Order::where('id', $orderId)
                    ->where('user_id', Auth::id())
                    ->firstOrFail();
    }

    private function verifyOrderHash(Order $order, $sessionHash)
    {
        $expectedHash = hash_hmac('sha256', $order->id . $order->order_number, config('app.key'));
        
        if ($sessionHash !== $expectedHash) {
            Log::error('Order hash mismatch', [
                'order_id' => $order->id,
                'user_id' => Auth::id()
            ]);
            return false;
        }
        
        return true;
    }

    private function completePayment(Order $order, $transactionId, $paymentGateway)
    {
        // Update payment status
        $payment = $order->payment()->first();
        if ($payment) {
            $payment->update([
                'status' => 'paid',
                'transaction_id' => $transactionId,
            ]);
        }

        // Update order status
        $order->update(['status' => 'processing']);

        // Reduce stock with idempotency check
        $this->reduceStock($order);

        // Log successful payment
        Log::info("{$paymentGateway} payment completed", [
            'user_id' => Auth::id(),
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'transaction_id' => $transactionId,
            'amount' => $order->total,
            'ip' => request()->ip()
        ]);
    }

    /**
     * Redirect to checkout page with error message
     */
    private function redirectToCheckoutWithError($message)
    {
        return redirect()
            ->route('checkout_page')
            ->with('error', $message);
    }

    /**
     * Create PayPal payment and redirect to PayPal
     */
    public function createPayPalPayment(Order $order, Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            // Recalculate from database to prevent manipulation
            $items = [];
            $itemsTotal = 0;

            foreach ($order->items as $orderItem) {
                $itemPrice = number_format($orderItem->price, 2, '.', '');
                $items[] = [
                    'name' => $orderItem->product_name . 
                             (isset($orderItem->size) ? ' (Size: ' . $orderItem->size . ')' : ''),
                    'quantity' => (string) $orderItem->quantity,
                    'unit_amount' => [
                        'currency_code' => 'PHP',
                        'value' => $itemPrice
                    ]
                ];
                $itemsTotal += ($orderItem->price * $orderItem->quantity);
            }

            // Add shipping as a line item
            $shippingFee = $order->shipping_fee ?? 0;
            if ($shippingFee > 0) {
                $items[] = [
                    'name' => 'Shipping Fee',
                    'quantity' => '1',
                    'unit_amount' => [
                        'currency_code' => 'PHP',
                        'value' => number_format($shippingFee, 2, '.', '')
                    ]
                ];
                $itemsTotal += $shippingFee;
            }

            $totalAmount = number_format($itemsTotal, 2, '.', '');

            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.success'),
                    "cancel_url" => route('paypal.cancel'),
                    "brand_name" => config('app.name'),
                    "landing_page" => "LOGIN",
                    "user_action" => "PAY_NOW"
                ],
                "purchase_units" => [
                    [
                        "reference_id" => $order->order_number,
                        "description" => "Order #{$order->order_number}",
                        "amount" => [
                            "currency_code" => "PHP",
                            "value" => $totalAmount,
                            "breakdown" => [
                                "item_total" => [
                                    "currency_code" => "PHP",
                                    "value" => $totalAmount
                                ]
                            ]
                        ],
                        "items" => $items,
                        "shipping" => [
                            "name" => [
                                "full_name" => $order->full_name
                            ],
                            "address" => [
                                "address_line_1" => $order->billing_address,
                                "admin_area_2" => $order->city,
                                "admin_area_1" => $order->province,
                                "postal_code" => $order->postal_code,
                                "country_code" => "PH"
                            ]
                        ]
                    ]
                ]
            ]);

            if (isset($response['id']) && $response['id'] != null) {
                session(['paypal_order_id' => $response['id']]);

                $payment = $order->payment()->first();
                if ($payment) {
                    $payment->update(['transaction_id' => $response['id']]);
                }

                Log::info('PayPal redirect', [
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'paypal_order_id' => $response['id'],
                    'ip' => $request->ip()
                ]);

                foreach ($response['links'] as $links) {
                    if ($links['rel'] === 'approve') {
                        return redirect()->away($links['href']);
                    }
                }

                return $this->redirectToCheckoutWithError('Something went wrong with PayPal.');
            }

            Log::error('PayPal Order Creation Failed', [
                'order_id' => $order->id,
                'response_status' => $response['status'] ?? 'unknown'
            ]);
            
            return $this->redirectToCheckoutWithError('Something went wrong with PayPal.');

        } catch (\Exception $e) {
            Log::error('PayPal payment creation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return $this->redirectToCheckoutWithError('PayPal payment failed. Please try again.');
        }
    }

    public function paypalSuccess(Request $request)
    {
        try {
            // Verify callback authenticity
            $sessionPayPalOrderId = session('paypal_order_id');
            $requestPayPalOrderId = $request->get('token');
            
            if (!$sessionPayPalOrderId || $sessionPayPalOrderId !== $requestPayPalOrderId) {
                Log::warning('PayPal callback verification failed', [
                    'session_order_id' => $sessionPayPalOrderId,
                    'request_token' => $requestPayPalOrderId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return redirect()->route('payment.success')
                    ->with([
                        'payment_failed' => true,
                        'error' => 'Invalid payment callback. Please try again.'
                    ]);
            }

            // Verify order ownership
            $orderId = session('order_id');
            $orderHash = session('order_hash');
            
            $order = $this->getVerifiedOrder($orderId);

            // Verify order hasn't been tampered with
            if (!$this->verifyOrderHash($order, $orderHash)) {
                return redirect()->route('payment.success')
                    ->with([
                        'payment_failed' => true,
                        'error' => 'Security verification failed.'
                    ]);
            }

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $paypalOrderId = $request->get('token');
            $response = $provider->capturePaymentOrder($paypalOrderId);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                // Verify payment amount matches order
                $paypalAmount = 0;
                if (isset($response['purchase_units'][0]['payments']['captures'][0]['amount']['value'])) {
                    $paypalAmount = (float) $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
                }
                
                $orderTotal = (float) $order->total;
                
                // Allow small rounding differences (0.01 peso = 1 centavo)
                if (abs($paypalAmount - $orderTotal) > 0.01) {
                    Log::error('PayPal amount mismatch', [
                        'order_id' => $order->id,
                        'order_total' => $orderTotal,
                        'paypal_amount' => $paypalAmount,
                        'difference' => abs($paypalAmount - $orderTotal)
                    ]);
                    
                    return redirect()->route('payment.success')
                        ->with([
                            'payment_failed' => true,
                            'error' => 'Payment amount verification failed.'
                        ]);
                }

                // Complete payment processing
                $this->completePayment($order, $response['id'], 'PayPal');

                // Load order with relationships for modal
                $order->load(['items', 'payment']);

                // Prepare order data for modal
                $orderData = [
                    'order_number' => $order->order_number,
                    'created_at' => $order->created_at->format('F j, Y'),
                    'full_name' => $order->full_name,
                    'phone' => $order->phone,
                    'province' => $order->province,
                    'city' => $order->city,
                    'barangay' => $order->barangay,
                    'postal_code' => $order->postal_code,
                    'billing_address' => $order->billing_address,
                    'total' => $order->total,
                    'shipping_fee' => $order->shipping_fee,
                    'payment_method' => $order->payment->method ?? 'paypal',
                    'items' => $order->items->map(function($item) {
                        return [
                            'product_name' => $item->product_name,
                            'size' => $item->size,
                            'quantity' => $item->quantity,
                            'total' => $item->total,
                        ];
                    })->toArray()
                ];

                // Clear checkout-specific session
                $this->clearCheckoutSession();

                // Redirect to success page with modal trigger
                return redirect()
                    ->route('payment.success')
                    ->with([
                        'success' => 'Payment successful! Your order has been placed.',
                        'show_modal' => true,
                        'order_data' => $orderData
                    ]);
            }

            Log::warning('PayPal payment not completed', [
                'status' => $response['status'] ?? 'unknown',
                'order_id' => session('order_id')
            ]);

            return redirect()->route('payment.success')
                ->with([
                    'payment_failed' => true,
                    'error' => 'Payment was not completed. Please try again.'
                ]);

        } catch (\Exception $e) {
            Log::error('PayPal success callback error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ip' => $request->ip()
            ]);
            
            return redirect()->route('payment.success')
                ->with([
                    'payment_failed' => true,
                    'error' => 'Payment verification failed. Please contact support.'
                ]);
        }
    }

    public function paypalCancel(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                if (session('order_id')) {
                    $order = Order::where('id', session('order_id'))
                                ->where('user_id', Auth::id())
                                ->lockForUpdate()
                                ->first();
                    
                    if ($order && $order->status === 'pending') {
                        Log::info('PayPal payment cancelled', [
                            'user_id' => Auth::id(),
                            'order_id' => $order->id,
                            'order_number' => $order->order_number,
                            'ip' => $request->ip()
                        ]);
                        
                        $order->delete();
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('PayPal cancel error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
        }

        $this->clearCheckoutSession();
        
        return redirect()->route('payment.success')
            ->with([
                'payment_failed' => true,
                'error' => 'Payment was cancelled. Your order has been removed.'
            ]);
    }

    /**
     * WEBHOOK: PayPal webhook verification endpoint
     * This provides an additional layer of security by verifying payments server-side
     */
    public function paypalWebhook(Request $request)
    {
        try {
            // Get webhook data
            $webhookData = $request->all();
            
            Log::info('PayPal webhook received', [
                'event_type' => $webhookData['event_type'] ?? 'unknown',
                'resource_type' => $webhookData['resource_type'] ?? 'unknown'
            ]);

            // Verify webhook signature
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            // Get webhook ID from config
            $webhookId = config('paypal.webhook.id');
            
            if (!$webhookId) {
                Log::warning('PayPal webhook ID not configured');
                return response()->json(['error' => 'Webhook not configured'], 500);
            }

            // Verify webhook signature using PayPal SDK
            $isValid = $this->verifyPayPalWebhookSignature($request, $webhookId);

            if (!$isValid) {
                Log::warning('Invalid PayPal webhook signature', [
                    'ip' => $request->ip(),
                    'headers' => $request->headers->all()
                ]);
                return response()->json(['error' => 'Invalid signature'], 400);
            }

            // Process webhook event
            $eventType = $webhookData['event_type'] ?? '';

            switch ($eventType) {
                case 'PAYMENT.CAPTURE.COMPLETED':
                    $this->handlePaymentCaptureCompleted($webhookData);
                    break;

                case 'PAYMENT.CAPTURE.DENIED':
                case 'PAYMENT.CAPTURE.REFUNDED':
                    $this->handlePaymentFailed($webhookData);
                    break;

                default:
                    Log::info('Unhandled PayPal webhook event', [
                        'event_type' => $eventType
                    ]);
            }

            return response()->json(['status' => 'success'], 200);

        } catch (\Exception $e) {
            Log::error('PayPal webhook processing error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);

            return response()->json(['error' => 'Webhook processing failed'], 500);
        }
    }

    /**
     * Verify PayPal webhook signature
     */
    private function verifyPayPalWebhookSignature(Request $request, $webhookId)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $token = $provider->getAccessToken();

            // Get required headers
            $headers = [
                'auth_algo' => $request->header('PAYPAL-AUTH-ALGO'),
                'cert_url' => $request->header('PAYPAL-CERT-URL'),
                'transmission_id' => $request->header('PAYPAL-TRANSMISSION-ID'),
                'transmission_sig' => $request->header('PAYPAL-TRANSMISSION-SIG'),
                'transmission_time' => $request->header('PAYPAL-TRANSMISSION-TIME'),
            ];

            // Check if all required headers are present
            if (in_array(null, $headers, true)) {
                Log::warning('Missing PayPal webhook headers', [
                    'headers' => array_keys(array_filter($headers, fn($v) => $v === null))
                ]);
                return false;
            }

            // Prepare verification request
            $verificationData = [
                'auth_algo' => $headers['auth_algo'],
                'cert_url' => $headers['cert_url'],
                'transmission_id' => $headers['transmission_id'],
                'transmission_sig' => $headers['transmission_sig'],
                'transmission_time' => $headers['transmission_time'],
                'webhook_id' => $webhookId,
                'webhook_event' => $request->all()
            ];

            // Call PayPal verification API
            $response = $provider->verifyWebHook($verificationData);

            if (isset($response['verification_status']) && $response['verification_status'] === 'SUCCESS') {
                Log::info('PayPal webhook signature verified');
                return true;
            }

            Log::warning('PayPal webhook verification failed', [
                'response' => $response
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error('PayPal webhook verification error', [
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Handle successful payment capture from webhook
     */
    private function handlePaymentCaptureCompleted($webhookData)
    {
        try {
            // Extract order reference from webhook
            $resource = $webhookData['resource'] ?? [];
            $supplementaryData = $resource['supplementary_data'] ?? [];
            $relatedIds = $supplementaryData['related_ids'] ?? [];
            $orderNumber = $relatedIds['order_id'] ?? null;

            if (!$orderNumber) {
                Log::warning('PayPal webhook: No order reference found');
                return;
            }

            // Find order
            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                Log::warning('PayPal webhook: Order not found', [
                    'order_number' => $orderNumber
                ]);
                return;
            }

            // Check if already processed
            if ($order->status === 'processing' || $order->stock_reduced_at) {
                Log::info('PayPal webhook: Order already processed', [
                    'order_id' => $order->id,
                    'status' => $order->status
                ]);
                return;
            }

            // Verify amount
            $captureAmount = (float) ($resource['amount']['value'] ?? 0);
            $orderTotal = (float) $order->total;

            if (abs($captureAmount - $orderTotal) > 0.01) {
                Log::error('PayPal webhook: Amount mismatch', [
                    'order_id' => $order->id,
                    'order_total' => $orderTotal,
                    'capture_amount' => $captureAmount
                ]);
                return;
            }

            // Process payment
            DB::transaction(function () use ($order, $resource) {
                $captureId = $resource['id'] ?? null;

                // Update payment
                $payment = $order->payment()->first();
                if ($payment && $payment->status !== 'paid') {
                    $payment->update([
                        'status' => 'paid',
                        'transaction_id' => $captureId,
                    ]);
                }

                // Update order
                if ($order->status === 'pending') {
                    $order->update(['status' => 'processing']);
                }

                // Reduce stock
                $this->reduceStock($order);

                Log::info('PayPal webhook: Payment processed successfully', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'capture_id' => $captureId
                ]);
            });

        } catch (\Exception $e) {
            Log::error('PayPal webhook: Payment processing error', [
                'error' => $e->getMessage(),
                'webhook_data' => $webhookData
            ]);
        }
    }

    /**
     * Handle failed or refunded payment from webhook
     */
    private function handlePaymentFailed($webhookData)
    {
        try {
            $resource = $webhookData['resource'] ?? [];
            $supplementaryData = $resource['supplementary_data'] ?? [];
            $relatedIds = $supplementaryData['related_ids'] ?? [];
            $orderNumber = $relatedIds['order_id'] ?? null;

            if (!$orderNumber) {
                return;
            }

            $order = Order::where('order_number', $orderNumber)->first();

            if (!$order) {
                return;
            }

            Log::info('PayPal webhook: Payment failed/refunded', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'event_type' => $webhookData['event_type']
            ]);

            // Update payment status
            $payment = $order->payment()->first();
            if ($payment) {
                $payment->update(['status' => 'failed']);
            }

            // Update order status
            if ($order->status === 'pending') {
                $order->update(['status' => 'cancelled']);
            }

        } catch (\Exception $e) {
            Log::error('PayPal webhook: Failed payment handling error', [
                'error' => $e->getMessage()
            ]);
        }
    }

    public function paymentSuccess()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view this page.');
        }

        return view('shop.payment-status');
    }

    /**
     * View order details page (for dashboard "Details" button)
     */
    public function viewOrderDetails($orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'payment', 'user'])
            ->firstOrFail();

        // Return the order details page
        return view('UserPanel.user-orders', compact('order'));
    }

    /**
     * Reduce product stock after successful payment (with idempotency)
     */
    private function reduceStock(Order $order)
    {
        // Idempotency check - prevent double stock reduction
        if ($order->stock_reduced_at) {
            Log::info('Stock already reduced for order', [
                'order_id' => $order->id,
                'reduced_at' => $order->stock_reduced_at
            ]);
            return true;
        }

        // Verify payment is complete before reducing stock
        $payment = $order->payment()->first();
        if (!$payment || $payment->status !== 'paid') {
            Log::warning('Attempted to reduce stock without paid payment', [
                'order_id' => $order->id,
                'payment_status' => $payment->status ?? 'no_payment'
            ]);
            return false;
        }

        try {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $orderItem) {
                    $product = Product::lockForUpdate()->find($orderItem->product_id);
                    
                    if ($product && $orderItem->size) {
                        $stockField = 'stock_' . strtolower($orderItem->size);
                        
                        if (isset($product->$stockField) && $product->$stockField >= $orderItem->quantity) {
                            $product->$stockField -= $orderItem->quantity;
                            $product->save();
                            
                            Log::info('Stock reduced', [
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                                'size' => $orderItem->size,
                                'quantity' => $orderItem->quantity,
                                'remaining_stock' => $product->$stockField
                            ]);
                        } else {
                            Log::error('Insufficient stock during reduction', [
                                'order_id' => $order->id,
                                'product_id' => $product->id,
                                'size' => $orderItem->size,
                                'required' => $orderItem->quantity,
                                'available' => $product->$stockField ?? 0
                            ]);
                        }
                    }
                }
                
                // Mark stock as reduced with timestamp
                $order->update(['stock_reduced_at' => now()]);
            });
            
            return true;
            
        } catch (\Exception $e) {
            Log::error('Stock reduction error', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }

    /**
     * Clear checkout session data (DRY principle)
     */
    private function clearCheckoutSession()
    {
        session()->forget([
            'paypal_order_id',
            'order_id',
            'order_hash',
            'cart',
            'validated_cart'
        ]);
    }
}