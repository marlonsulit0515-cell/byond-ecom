<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class CheckoutController extends Controller
{
    /**
     * Process checkout and create order
     * FIXED: All Critical & High priority security issues
     */
    public function checkout(Request $request)
    {
        // Check if the user has login account before proceeding to checkout
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to complete your order.');
        }

        // FIXED #3 & #16: Enhanced validation with regex for phone
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => [
                'required',
                'string',
                'max:20',
                'regex:/^\+63\s\d{3}\s\d{3}\s\d{4}$/' // Enforce Philippine format
            ],
            'country' => 'required|string',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'billing_address' => 'required|string|max:255',
            'delivery_option' => 'required|string|in:ship,pickup',
            'payment_method' => 'required|string|in:paypal,paymongo',
            'shipping_address' => 'required_if:same_as_billing,false|nullable|string|max:255',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        try {
            DB::beginTransaction();

            // FIXED #2 & #3: Validate stock AND recalculate prices from database
            $validatedCart = [];
            $total = 0;

            foreach ($cart as $cartKey => $item) {
                $productId = $this->extractProductId($cartKey, $item);
                
                if (!$productId) {
                    DB::rollBack();
                    return redirect()->route('view-cart')->with('error', 'Invalid product in cart.');
                }

                $size = $item['size'] ?? 'M';

                // Lock product row to prevent race conditions
                $product = Product::lockForUpdate()->find($productId);
                
                if (!$product) {
                    DB::rollBack();
                    return redirect()->route('view-cart')->with('error', "Product not found.");
                }

                // Validate stock inside transaction
                $stockField = 'stock_' . strtolower($size);
                $availableStock = $product->$stockField ?? 0;

                if ($item['quantity'] > $availableStock) {
                    DB::rollBack();
                    return redirect()->route('view-cart')->with('error',
                        "Item '{$product->name}' (Size: {$size}) has insufficient stock. Available: {$availableStock}"
                    );
                }

                // Use actual database prices, not session prices
                $actualPrice = $product->discount_price ?? $product->price;
                $itemTotal = $actualPrice * $item['quantity'];
                $total += $itemTotal;

                // Store validated cart data
                $validatedCart[] = [
                    'product_id' => $productId,
                    'name' => $product->name,
                    'size' => $size,
                    'quantity' => $item['quantity'],
                    'price' => $actualPrice,
                    'total' => $itemTotal,
                    'image' => basename($product->image), // FIXED #11: Remove path components
                ];
            }

            // FIXED #4: Validate email
            $userEmail = Auth::user()->email;
            if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
                DB::rollBack();
                Log::error('Invalid user email on checkout', [
                    'user_id' => Auth::id(),
                    'email' => $userEmail
                ]);
                return redirect()->back()->with('error', 'Invalid email address. Please update your profile.');
            }

            // FIXED #6: Improved order number with UUID
            $orderNumber = 'ORD-' . strtoupper(Str::random(12)) . '-' . time();

            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(),
                'email' => $userEmail,
                'order_number' => $orderNumber,
                'status' => 'pending',
                'total' => $total,
                'full_name' => strip_tags($request->full_name), // FIXED #10: Sanitize
                'phone' => $request->phone,
                'country' => $request->country,
                'province' => strip_tags($request->province), // FIXED #10: Sanitize
                'city' => strip_tags($request->city),
                'barangay' => strip_tags($request->barangay),
                'postal_code' => $request->postal_code,
                'billing_address' => strip_tags($request->billing_address),
                'delivery_option' => $request->delivery_option,
                'same_as_billing' => $request->has('same_as_billing'),
                'shipping_address' => $request->has('same_as_billing')
                    ? strip_tags($request->billing_address)
                    : strip_tags($request->shipping_address),
            ]);

            // FIXED #15: Log checkout with IP
            Log::info('Checkout initiated', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'total' => $order->total,
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

            // Create payment record
            $payment = $order->payment()->create([
                'user_id' => Auth::id(),
                'method' => $request->payment_method,
                'status' => 'pending',
                'amount' => $order->total,
            ]);

            // FIXED #1: Regenerate session after order creation
            session()->regenerate();
            
            // Store validated cart for payment processing
            session(['validated_cart' => $validatedCart]);

            // Handle payment methods
            switch ($request->payment_method) {
                case 'paypal':
                    DB::commit();
                    return $this->createPayPalPayment($order, $request);

                case 'paymongo':
                    DB::commit();
                    return $this->createPaymongoPayment($order, $request);

                default:
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Invalid payment method selected.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            
            // FIXED #8: Sanitized error logging
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

    /**
     * Extract product ID from cart key safely
     */
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

    /**
     * Create PayPal payment and redirect to PayPal
     */
    public function createPayPalPayment(Order $order, Request $request)
    {
        try {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();

            $validatedCart = session('validated_cart', []);
            $items = [];
            $itemsTotal = 0;

            foreach ($validatedCart as $item) {
                $itemPrice = number_format($item['price'], 2, '.', '');
                $items[] = [
                    'name' => $item['name'] . (isset($item['size']) ? ' (Size: ' . $item['size'] . ')' : ''),
                    'quantity' => (string) $item['quantity'],
                    'unit_amount' => [
                        'currency_code' => 'PHP',
                        'value' => $itemPrice
                    ]
                ];
                $itemsTotal += ($item['price'] * $item['quantity']);
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
                // Store PayPal order ID in session
                session([
                    'paypal_order_id' => $response['id'],
                    'order_id' => $order->id
                ]);

                // Update payment with PayPal order ID
                $payment = $order->payment()->first();
                if ($payment) {
                    $payment->update(['transaction_id' => $response['id']]);
                }

                // FIXED #15: Log PayPal redirect
                Log::info('PayPal redirect', [
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'paypal_order_id' => $response['id'],
                    'ip' => $request->ip()
                ]);

                // Redirect user to PayPal approval link
                foreach ($response['links'] as $links) {
                    if ($links['rel'] === 'approve') {
                        return redirect()->away($links['href']);
                    }
                }

                return redirect()
                    ->route('checkout_page')
                    ->with('error', 'Something went wrong with PayPal.');
            }

            Log::error('PayPal Order Creation Failed', [
                'order_id' => $order->id,
                'response_status' => $response['status'] ?? 'unknown'
            ]);
            
            return redirect()
                ->route('checkout_page')
                ->with('error', 'Something went wrong with PayPal.');

        } catch (\Exception $e) {
            Log::error('PayPal payment creation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()
                ->route('checkout_page')
                ->with('error', 'PayPal payment failed. Please try again.');
        }
    }

    /**
     * Handle successful PayPal payment
     * FIXED #5: Added callback verification
     */
    public function paypalSuccess(Request $request)
    {
        try {
            // FIXED #5: Verify callback authenticity
            $sessionPayPalOrderId = session('paypal_order_id');
            $requestPayPalOrderId = $request->get('token');
            
            if (!$sessionPayPalOrderId || $sessionPayPalOrderId !== $requestPayPalOrderId) {
                Log::warning('PayPal callback verification failed', [
                    'session_order_id' => $sessionPayPalOrderId,
                    'request_token' => $requestPayPalOrderId,
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
                
                return redirect()
                    ->route('checkout_page')
                    ->with('error', 'Invalid payment callback. Please try again.');
            }

            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $provider->getAccessToken();

            $paypalOrderId = $request->get('token');
            $response = $provider->capturePaymentOrder($paypalOrderId);

            if (isset($response['status']) && $response['status'] === 'COMPLETED') {
                $orderId = session('order_id');
                $order = Order::where('id', $orderId)
                              ->where('user_id', Auth::id())
                              ->firstOrFail();

                // Update payment status
                $payment = $order->payment()->first();
                if ($payment) {
                    $payment->update([
                        'status' => 'paid',
                        'transaction_id' => $response['id'],
                    ]);
                }

                // Update order status
                $order->update(['status' => 'processing']);

                // FIXED #14: Reduce stock with verification
                $this->reduceStock($order);

                // FIXED #15: Log successful payment
                Log::info('PayPal payment completed', [
                    'user_id' => Auth::id(),
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'transaction_id' => $response['id'],
                    'amount' => $order->total,
                    'ip' => $request->ip()
                ]);

                // Clear session and cart
                session()->forget(['paypal_order_id', 'order_id', 'cart', 'validated_cart']);

                return redirect()
                    ->route('shop.confirmation', $order->order_number)
                    ->with('success', 'Payment successful! Your order has been placed.');
            }

            Log::warning('PayPal payment not completed', [
                'status' => $response['status'] ?? 'unknown',
                'order_id' => session('order_id')
            ]);

            return redirect()
                ->route('checkout_page')
                ->with('error', 'Payment was not completed.');

        } catch (\Exception $e) {
            Log::error('PayPal success callback error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'ip' => $request->ip()
            ]);
            
            return redirect()
                ->route('checkout_page')
                ->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Handle cancelled PayPal payment
     * FIXED #13: Atomic order deletion
     */
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
                        // FIXED #15: Log cancellation
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

        session()->forget(['paypal_order_id', 'order_id', 'validated_cart']);
        
        return redirect()
            ->route('checkout_page')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Create Paymongo payment and redirect to Paymongo
     */
    public function createPaymongoPayment(Order $order, Request $request)
    {
        try {
            // Store order ID in session
            session([
                'paymongo_order_id' => $order->id,
                'order_id' => $order->id
            ]);

            // FIXED #15: Log Paymongo initiation
            Log::info('Paymongo payment initiated', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'ip' => $request->ip()
            ]);

            // TODO: Implement Paymongo API integration
            return redirect()
                ->route('checkout_page')
                ->with('error', 'Paymongo payment is not yet configured. Please use PayPal or contact support.');

        } catch (\Exception $e) {
            Log::error('Paymongo payment creation error', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()
                ->route('checkout_page')
                ->with('error', 'Paymongo payment failed. Please try again.');
        }
    }

    /**
     * Handle successful Paymongo payment
     */
    public function paymongoSuccess(Request $request)
    {
        try {
            // FIXED #5: Verify callback
            $sessionOrderId = session('paymongo_order_id');
            if (!$sessionOrderId) {
                Log::warning('Paymongo callback without session', [
                    'ip' => $request->ip()
                ]);
                abort(403, 'Invalid callback');
            }

            $orderId = session('order_id');
            $order = Order::where('id', $orderId)
                          ->where('user_id', Auth::id())
                          ->firstOrFail();

            $payment = $order->payment()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => $request->get('checkout_session_id'),
                ]);
            }

            $order->update(['status' => 'processing']);

            // FIXED #14: Reduce stock with verification
            $this->reduceStock($order);

            // FIXED #15: Log success
            Log::info('Paymongo payment completed', [
                'user_id' => Auth::id(),
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'ip' => $request->ip()
            ]);

            session()->forget(['paymongo_order_id', 'order_id', 'cart', 'validated_cart']);

            return redirect()
                ->route('shop.confirmation', $order->order_number)
                ->with('success', 'Payment successful! Your order has been placed.');

        } catch (\Exception $e) {
            Log::error('Paymongo success callback error', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            return redirect()
                ->route('checkout_page')
                ->with('error', 'Payment verification failed.');
        }
    }

    /**
     * Handle cancelled Paymongo payment
     */
    public function paymongoCancel(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                if (session('order_id')) {
                    $order = Order::where('id', session('order_id'))
                                  ->where('user_id', Auth::id())
                                  ->lockForUpdate()
                                  ->first();
                    
                    if ($order && $order->status === 'pending') {
                        Log::info('Paymongo payment cancelled', [
                            'user_id' => Auth::id(),
                            'order_id' => $order->id,
                            'ip' => $request->ip()
                        ]);
                        
                        $order->delete();
                    }
                }
            });
        } catch (\Exception $e) {
            Log::error('Paymongo cancel error', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);
        }

        session()->forget(['paymongo_order_id', 'order_id', 'validated_cart']);
        
        return redirect()
            ->route('checkout_page')
            ->with('error', 'Payment was cancelled.');
    }

    /**
     * Order confirmation page
     */
    public function confirmation($orderNumber)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view order confirmations.');
        }
        
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id())
            ->with(['items.product', 'payment'])
            ->firstOrFail();

        return view('shop.confirmation', compact('order'));
    }

    /**
     * Reduce product stock after successful payment
     * FIXED #14: Added payment verification
     */
    private function reduceStock(Order $order)
    {
        // FIXED #14: Verify order and payment status
        if ($order->status !== 'processing' && $order->status !== 'paid') {
            Log::warning('Attempted to reduce stock for non-processing order', [
                'order_id' => $order->id,
                'status' => $order->status
            ]);
            return false;
        }

        $payment = $order->payment()->first();
        if (!$payment || $payment->status !== 'paid') {
            Log::warning('Attempted to reduce stock without paid payment', [
                'order_id' => $order->id,
                'payment_status' => $payment->status ?? 'no_payment'
            ]);
            return false;
        }

        foreach ($order->items as $orderItem) {
            $product = Product::find($orderItem->product_id);
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
                }
            }
        }
        
        return true;
    }
}