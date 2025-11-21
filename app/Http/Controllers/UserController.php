<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $product = Product::select('id', 'name', 'price', 'discount_price', 'image', 'hover_image')
                        ->limit(20)
                        ->get();
        
        return view('home', compact('product'));
    }

    public function user_dashboard()
    {
        return view('UserPanel.userdash');
    }

    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user orders with relationships
        $orders = $user->orders()
            ->with(['items.product', 'payment'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get recent orders (last 5)
        $recentOrders = $user->orders()
            ->with(['items', 'payment'])
            ->latest()
            ->limit(5)
            ->get();

        // Order statistics
        $orderStats = [
            'total_orders' => $user->orders()->count(),
            'pending_orders' => $user->orders()->where('status', 'pending')->count(),
            'processing_orders' => $user->orders()->where('status', 'processing')->count(),
            'completed_orders' => $user->orders()->where('status', 'completed')->count(),
            'total_spent' => $user->orders()->where('status', 'completed')->sum('total')
        ];

        return view('UserPanel.userdash', compact('orders', 'recentOrders', 'orderStats'));
    }

    public function orders(Request $request)
{
    $user = Auth::user();
    
    $query = $user->orders()->with(['items.product', 'payment']);

    // Filter by status
    if ($request->filled('status') && $request->status !== 'all') {
        $query->where('status', $request->status);
    }

    // Search by order number
    if ($request->filled('search')) {
        $query->where('order_number', 'like', '%' . $request->search . '%');
    }

    // Date range filter
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }

    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    $orders = $query->orderBy('created_at', 'desc')->paginate(15);

    // Return only the orders container HTML for AJAX requests
    if ($request->ajax()) {
        return response()->json([
            'success' => true,
            'html' => view('UserPanel.order-table', compact('orders'))->render()
        ]);
    }

    return view('UserPanel.user-orders', compact('orders'));
}


    /**
     * Get order details as JSON for modal display
     */
    public function orderDetails($id)
    {
        // Log the request for debugging
        Log::info('Order details requested', [
            'order_id' => $id,
            'user_id' => Auth::id()
        ]);

        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        try {
            $user = Auth::user();
            
            // Check if order exists and get the latest status log
            $order = $user->orders()
                ->with(['items.product', 'payment'])
                ->findOrFail($id);

            // Get the most recent status change from OrderStatusLog
            $latestStatusLog = OrderStatusLog::where('order_id', $order->id)
                ->orderBy('changed_at', 'desc')
                ->first();

            // Prepare order data for JSON response
            $orderData = [
                'order_number' => $order->order_number,
                'tracking_number' => $order->tracking_number ?? null,
                'created_at' => $order->created_at->format('F j, Y - g:i A'),
                'last_status_update' => $latestStatusLog 
                    ? $latestStatusLog->changed_at->format('F j, Y - g:i A')
                    : $order->updated_at->format('F j, Y - g:i A'),
                'status' => $order->status,
                'full_name' => $order->full_name,
                'phone' => $order->phone,
                'country' => $order->country ?? 'Philippines',
                'province' => $order->province,
                'city' => $order->city,
                'barangay' => $order->barangay,
                'postal_code' => $order->postal_code,
                'billing_address' => $order->billing_address,
                'delivery_option' => $order->delivery_option,
                'total' => $order->total,
                'shipping_fee' => $order->shipping_fee ?? 0,
                'payment_method' => $order->payment->method ?? 'N/A',
                'payment_status' => $order->payment->status ?? 'pending',
                'transaction_id' => $order->payment->transaction_id ?? null,
                'items' => $order->items->map(function($item) {
                    return [
                        'product_name' => $item->product_name,
                        'size' => $item->size,
                        'quantity' => $item->quantity,
                        'price' => $item->price,
                        'total' => $item->total,
                    ];
                })->toArray()
            ];

            Log::info('Order details fetched successfully', [
                'order_id' => $id,
                'order_number' => $order->order_number
            ]);

            return response()->json([
                'success' => true,
                'order' => $orderData
            ]);

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            Log::error('Order not found', [
                'order_id' => $id,
                'user_id' => Auth::id()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Order not found'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Error fetching order details', [
                'order_id' => $id,
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load order details: ' . $e->getMessage()
            ], 500);
        }
    }

    //controller for cancellation request
    public function requestCancellation($id, Request $request)
    {
        try {
            $user = Auth::user();

            // Find the order
            $order = $user->orders()->findOrFail($id);

            // Only allow cancellation request if order is pending or processing
            if (!in_array($order->status, ['pending', 'processing'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order cannot be cancelled at this stage.'
                ], 400);
            }

            // Check if already requested
            if ($order->status === 'cancellation_requested') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cancellation has already been requested for this order.'
                ], 400);
            }

            // Validate the request
            $validated = $request->validate([
                'reason' => 'required|string|in:changed_mind,found_better_price,ordered_by_mistake,delivery_too_long,want_different_product,payment_issues,duplicate_order,other',
                'other_reason' => 'required_if:reason,other|nullable|string|min:10|max:500',
                'comments' => 'nullable|string|max:1000'
            ]);

            // Prepare the cancellation reason text
            $cancellationReasonText = '';
            
            if ($validated['reason'] === 'other') {
                // If "other" is selected, use the custom reason
                $cancellationReasonText = $validated['other_reason'];
            } else {
                // Convert reason code to readable text
                $reasonMap = [
                    'changed_mind' => 'Changed my mind',
                    'found_better_price' => 'Found a better price elsewhere',
                    'ordered_by_mistake' => 'Ordered by mistake',
                    'delivery_too_long' => 'Delivery time is too long',
                    'want_different_product' => 'Want to order a different product',
                    'payment_issues' => 'Payment issues',
                    'duplicate_order' => 'Duplicate order'
                ];
                
                $cancellationReasonText = $reasonMap[$validated['reason']] ?? ucfirst(str_replace('_', ' ', $validated['reason']));
            }

            // Start database transaction
            DB::beginTransaction();
            
            try {
                // Update the order
                $order->update([
                    'status' => 'cancellation_requested',
                    'cancellation_reason' => $cancellationReasonText,
                    'cancellation_comments' => $validated['comments']
                ]);
                
                // Log the status change
                OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => 'cancellation_requested',
                    'changed_at' => now(),
                    'notes' => 'Customer requested cancellation: ' . $cancellationReasonText . 
                            ($validated['comments'] ? ' | Comments: ' . $validated['comments'] : '')
                ]);
                
                // Commit transaction
                DB::commit();
                
                return response()->json([
                    'success' => true,
                    'message' => 'Cancellation request submitted successfully. Our team will review your request shortly.',
                    'orderNumber' => $order->order_number
                ]);
                
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Please fill in all required fields correctly.',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found.'
            ], 404);
            
        } catch (\Exception $e) {
            Log::error('Cancellation request error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing your request. Please try again.'
            ], 500);
        }
    }

    public function confirmDelivery($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()->findOrFail($id);
        
        // Only allow confirmation if order is shipped
        if ($order->status !== 'shipped') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not yet shipped or already completed.'
            ], 400);
        }

        DB::transaction(function() use ($order) {
            $order->update(['status' => 'completed']);
            
            // Log the status change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'completed',
                'changed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Delivery confirmed successfully. Thank you for your order!',
            'orderNumber' => $order->order_number
        ]);
    }

    public function profile()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:500'
        ]);

        $user->update($request->only(['name', 'email', 'phone', 'address']));

        return redirect()->route('profile.edit')
            ->with('success', 'Profile updated successfully!');
    }
}