<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Srmklive\PayPal\Services\PayPal as PayPalClient;

class CheckoutController extends Controller
{
    public function checkout(Request $request)
    {
        // Check authentication
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to complete your order.');
        }

        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'country' => 'required|string',
            'province' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'barangay' => 'required|string|max:255',
            'postal_code' => 'required|string|max:10',
            'billing_address' => 'required|string|max:255',
            'delivery_option' => 'required|string|in:ship,pickup',
            'payment_method' => 'required|string',
        ]);

        $cart = session('cart', []);
        if (empty($cart)) {
            return redirect()->back()->with('error', 'Your cart is empty.');
        }

        // Validate stock before proceeding
        foreach ($cart as $cartKey => $item) {
            $productId = is_numeric($cartKey) ? $cartKey : ($item['product_id'] ?? explode('_', $cartKey)[0]);
            $size = $item['size'] ?? 'M';

            $product = Product::find($productId);
            if ($product) {
                $stockField = 'stock_' . strtolower($size);
                $availableStock = $product->$stockField ?? 0;

                if ($item['quantity'] > $availableStock) {
                    return redirect()->route('view-cart')->with('error',
                        "Item '{$item['name']}' (Size: {$size}) has insufficient stock. Available: {$availableStock}, Requested: {$item['quantity']}"
                    );
                }
            }
        }

        try {
            DB::beginTransaction();

            // Calculate total
            $total = collect($cart)->sum(fn($item) => $item['price'] * $item['quantity']);

            // Create Order - Only for authenticated users
            $order = Order::create([
                'user_id' => Auth::id(),
                'email' => Auth::user()->email,
                'order_number' => 'ORD-' . strtoupper(Str::random(8)),
                'status' => 'pending',
                'total' => $total,
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'country' => $request->country ?? 'Philippines',
                'province' => $request->province,
                'city' => $request->city,
                'barangay' => $request->barangay,
                'postal_code' => $request->postal_code,
                'billing_address' => $request->billing_address,
                'delivery_option' => $request->delivery_option,
                'same_as_billing' => $request->has('same_as_billing'),
                'shipping_address' => $request->has('same_as_billing')
                    ? $request->billing_address
                    : $request->shipping_address,
            ]);

            // Save Order Items
            foreach ($cart as $cartKey => $item) {
                $productId = is_numeric($cartKey) ? $cartKey : ($item['product_id'] ?? explode('_', $cartKey)[0]);
                
                $order->items()->create([
                    'product_id' => $productId,
                    'product_name' => $item['name'],
                    'size' => $item['size'] ?? null,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'total' => $item['price'] * $item['quantity'],
                ]);
            }

            // Handle different payment methods
            switch ($request->payment_method) {
                case 'paypal':
                    $payment = $order->payment()->create([
                        'user_id' => Auth::id(), 
                        'method' => 'paypal',
                        'status' => 'pending',
                        'amount' => $order->total,
                    ]);
                    DB::commit();
                    return $this->createPayPalPayment($order);

                case 'gcash':
                    $payment = $order->payment()->create([
                        'user_id' => Auth::id(),
                        'method' => 'gcash',
                        'status' => 'pending',
                        'amount' => $order->total,
                    ]);
                    DB::commit();
                    return $this->createGCashPayment($order);

                case 'maya':
                    $payment = $order->payment()->create([
                        'user_id' => Auth::id(),
                        'method' => 'maya',
                        'status' => 'pending',
                        'amount' => $order->total,
                    ]);
                    DB::commit();
                    return $this->createMayaPayment($order);

                default:
                    // For cash or other manual payment methods
                    $payment = $order->payment()->create([
                        'user_id' => Auth::id(),
                        'method' => $request->payment_method,
                        'status' => 'pending',
                        'amount' => $order->total,
                    ]);
                    DB::commit();
                    
                    // Clear cart
                    session()->forget('cart');

                    return redirect()
                        ->route('shop.confirmation', $order->order_number)
                        ->with('success', 'Order placed successfully! Your order number is ' . $order->order_number);
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Checkout error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Something went wrong. Please try again.');
        }
    }

    public function createPayPalPayment(Order $order)
{
    try {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();

        // Prepare cart items for PayPal
        $cart = session('cart', []);
        $items = [];

        foreach ($cart as $id => $item) {
            $items[] = [
                'name' => $item['name'] . (isset($item['size']) ? ' (Size: ' . $item['size'] . ')' : ''),
                'quantity' => (string) $item['quantity'],
                'unit_amount' => [
                    'currency_code' => 'PHP',
                    'value' => number_format($item['price'], 2, '.', '')
                ]
            ];
        }

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
                        "value" => number_format((float)$order->total, 2, '.', ''),
                        "breakdown" => [
                            "item_total" => [
                                "currency_code" => "PHP",
                                "value" => number_format((float)$order->total, 2, '.', '')
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

            // Redirect user to PayPal approval link
            foreach ($response['links'] as $links) {
                if ($links['rel'] === 'approve') {
                    return redirect()->away($links['href']);
                }
            }

            return redirect()
                ->route('checkout.process')
                ->with('error', 'Something went wrong with PayPal.');
        }

        Log::error('PayPal Order Creation Failed', $response);
        return redirect()
            ->route('checkout.process')
            ->with('error', 'Something went wrong with PayPal.');

    } catch (\Exception $e) {
        Log::error('PayPal payment creation error: ' . $e->getMessage());
        return redirect()
            ->route('checkout.process')
            ->with('error', 'PayPal payment failed. Please try again.');
    }
}


    public function paypalSuccess(Request $request)
{
    try {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $provider->getAccessToken();

        // PayPal sends 'token' in the query string, which is actually the orderID
        $paypalOrderId = $request->get('token');
        $response = $provider->capturePaymentOrder($paypalOrderId);

        if (isset($response['status']) && $response['status'] === 'COMPLETED') {
            // Retrieve our order from session
            $orderId = session('order_id');
            $order = Order::findOrFail($orderId);

            // Update payment status
            $payment = $order->payment()->first();
            if ($payment) {
                $payment->update([
                    'status' => 'paid',
                    'transaction_id' => $response['id'], // PayPal capture ID
                ]);
            }

            // Update order status
            $order->update(['status' => 'processing']);

            // Reduce stock after successful payment
            $this->reduceStock($order);

            // Clear session and cart
            session()->forget(['paypal_order_id', 'order_id', 'cart']);

            return redirect()
                ->route('shop.confirmation', $order->order_number)
                ->with('success', 'Payment successful! Your order has been placed.');
        }

        return redirect()
            ->route('checkout.process')
            ->with('error', 'Payment was not completed.');

    } catch (\Exception $e) {
        Log::error('PayPal success callback error: ' . $e->getMessage());
        return redirect()
            ->route('checkout.process')
            ->with('error', 'Payment verification failed.');
    }
}


    public function paypalCancel()
    {
        // Get order from session and delete it since payment was cancelled
        if (session('order_id')) {
            $order = Order::find(session('order_id'));
            if ($order) {
                $order->delete(); // This will also delete related records due to cascade
            }
        }

        session()->forget(['paypal_order_id', 'order_id']);
        
        return redirect()
            ->route('checkout.process')
            ->with('error', 'Payment was cancelled.');
    }

    public function confirmation($orderNumber)
    {
        // Ensure only authenticated users can view confirmations
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'You must be logged in to view order confirmations.');
        }
        
        $order = Order::where('order_number', $orderNumber)
            ->where('user_id', Auth::id()) // Only allow users to view their own orders
            ->with(['items.product', 'payment']) // Load product relationship
            ->firstOrFail();

        return view('shop.confirmation', compact('order'));
    }

    private function reduceStock(Order $order)
    {
        foreach ($order->items as $orderItem) {
            $product = Product::find($orderItem->product_id);
            if ($product && $orderItem->size) {
                $stockField = 'stock_' . strtolower($orderItem->size);
                if ($product->$stockField >= $orderItem->quantity) {
                    $product->$stockField -= $orderItem->quantity;
                    $product->save();
                }
            }
        }
    }

    /**
     * Handle Maya payment redirection or instructions.
     */
    public function createMayaPayment(Order $order)
    {
        // For demonstration, simply clear the cart and redirect to confirmation.
        // Replace this with actual Maya integration as needed.
        session()->forget('cart');
        return redirect()
            ->route('shop.confirmation', $order->order_number)
            ->with('success', 'Order placed successfully! Please follow the Maya payment instructions.');
    }

    /**
     * Handle GCash payment redirection or instructions.
     */
    public function createGCashPayment(Order $order)
    {
        // For demonstration, simply clear the cart and redirect to confirmation.
        // Replace this with actual GCash integration as needed.
        session()->forget('cart');
        return redirect()
            ->route('shop.confirmation', $order->order_number)
            ->with('success', 'Order placed successfully! Please follow the GCash payment instructions.');
    }
}