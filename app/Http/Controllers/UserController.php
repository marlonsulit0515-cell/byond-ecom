<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderStatusLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Only select needed columns
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
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Search by order number
        if ($request->has('search') && !empty($request->search)) {
            $query->where('order_number', 'like', '%' . $request->search . '%');
        }

        // Date range filter
        if ($request->has('date_from') && !empty($request->date_from)) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->has('date_to') && !empty($request->date_to)) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $orders = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('UserPanel.user-orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()
            ->with(['items.product', 'payment', 'statusHistory'])
            ->findOrFail($id);

        return view('UserPanel.order-reciept', compact('order'));
    }

    public function getOrderStatus($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()
            ->with(['payment', 'statusHistory'])
            ->findOrFail($id);

        return response()->json([
            'status' => $order->status,
            'payment_status' => $order->payment->status ?? 'pending',
            'last_updated' => $order->updated_at->format('M d, Y g:i A'),
            'status_history' => $order->statusHistory ? $order->statusHistory->map(function($history) {
                return [
                    'status' => $history->status,
                    'created_at' => $history->created_at->format('M d, Y g:i A'),
                    'note' => $history->note ?? null
                ];
            }) : []
        ]);
    }

    /**
     * Request cancellation (User requests to cancel order)
     * Only allows requesting cancellation for pending or processing orders
     */
    public function requestCancellation($id)
    {
        $user = Auth::user();
        
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

        DB::transaction(function() use ($order) {
            $order->update(['status' => 'cancellation_requested']);
            
            // Log the status change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'cancellation_requested',
                'changed_at' => now(),
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Cancellation request submitted successfully. Waiting for admin approval.',
            'orderNumber' => $order->order_number
        ]);
    }

    /**
     * Confirm delivery (User marks order as received)
     * Only allows confirmation if order is shipped
     */
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