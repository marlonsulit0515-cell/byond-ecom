<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controller as BaseController;

class UserController extends BaseController
{
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
        return view('UserPanel.userdash'); // Assuming you have a view named 'shop.shop-page'
    }
    public function __construct()
    {
        $this->middleware('auth');
    }

    // Fixed myOrders method
    public function myOrders()
    {
        $user = Auth::user();

        $orders = Order::with(['items', 'payment'])
            ->where('user_id', $user->id)
            ->latest()
            ->get();

        return view('UserPanel.user-orders', compact('orders'));
    }

    // Your dashboard method (looks good)
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

    // Your orderDetails method (looks good)
    public function orderDetails($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()
            ->with(['items.product', 'payment', 'statusHistory'])
            ->findOrFail($id);

        return view('UserPanel.order-reciept', compact('order'));
    }

    // Your getOrderStatus method (looks good)
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

    // Additional methods you might need:

    public function cancelOrder($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()->findOrFail($id);
        
        // Only allow cancellation if order is pending
        if ($order->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Order cannot be cancelled at this stage.'
            ]);
        }

        $order->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Order cancelled successfully.'
        ]);
    }

    public function confirmDelivery($id)
    {
        $user = Auth::user();
        
        $order = $user->orders()->findOrFail($id);
        
        // Only allow confirmation if order is delivered
        if ($order->status !== 'delivered') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not yet delivered.'
            ]);
        }

        $order->update(['status' => 'completed']);

        return response()->json([
            'success' => true,
            'message' => 'Delivery confirmed successfully.'
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
