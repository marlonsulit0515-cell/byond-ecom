<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment']);

        // Filter by status if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Search by order number, customer name, or email
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                ->orWhere('full_name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('email', 'like', "%{$search}%")
                            ->orWhere('name', 'like', "%{$search}%");
                });
            });
        }

        // Apply sorting
        switch ($request->sort) {
            case 'total_asc':
                $query->orderBy('total', 'asc');
                break;
            case 'total_desc':
                $query->orderBy('total', 'desc');
                break;
            case 'date_asc':
                $query->orderBy('created_at', 'asc');
                break;
            case 'date_desc':
                $query->orderBy('created_at', 'desc');
                break;
            default:
                // Default to latest (newest first)
                $query->orderBy('created_at', 'desc');
        }

        $orders = $query->paginate(30);

        // Global count (ignores filters)
        $allOrdersCount = Order::count();

        // Per-status counts
        $statusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->map(fn($count) => (int) $count)
            ->toArray();

        // Ensure all statuses are present
        $statusCounts = array_merge([
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'cancellation_requested' => 0,
        ], $statusCounts);

        return view('AdminPanel.orders.index', compact('orders', 'statusCounts', 'allOrdersCount'));
    }

    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment', 'statusLogs']);

        return view('AdminPanel.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        // Validate based on the new status
        $rules = [
            'status' => 'required|in:pending,processing,shipped,completed,cancelled,cancellation_requested',
            'notes' => 'nullable|string|max:500'
        ];

        // If status is being changed to 'shipped', tracking number is required
        if ($request->status === 'shipped') {
            $rules['tracking_number'] = 'required|string|max:100';
        }

        $validated = $request->validate($rules);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Use DB transaction for data consistency
        DB::transaction(function() use ($order, $oldStatus, $newStatus, $validated) {
            // Handle stock logic
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->restoreStock($order);
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                $this->deductStock($order);
            }

            // Update order data
            $updateData = ['status' => $newStatus];
            
            // Add tracking number if status is shipped
            if ($newStatus === 'shipped' && isset($validated['tracking_number'])) {
                $updateData['tracking_number'] = $validated['tracking_number'];
            }

            $order->update($updateData);

            // Log the status change (without tracking number)
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status'   => $newStatus,
                'changed_at' => now(),
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'oldStatus' => $oldStatus,
                'orderNumber' => $order->order_number,
                'tracking_number' => $order->tracking_number,
                'new_status_label' => ucfirst(str_replace('_', ' ', $order->status)),
                'status_counts' => [
                    'pending'   => Order::where('status', 'pending')->count(),
                    'processing'=> Order::where('status', 'processing')->count(),
                    'shipped'   => Order::where('status', 'shipped')->count(),
                    'completed' => Order::where('status', 'completed')->count(),
                    'cancelled' => Order::where('status', 'cancelled')->count(),
                    'cancellation_requested' => Order::where('status', 'cancellation_requested')->count(),
                ],
                'message' => "Order status updated to " . str_replace('_', ' ', $newStatus)
            ]);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order #{$order->order_number} status updated to " . str_replace('_', ' ', $newStatus));
    }

    public function getStatusCounts()
    {
        $statusCounts = Order::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        // Ensure all statuses are always present
        $statusCounts = array_merge([
            'pending' => 0,
            'processing' => 0,
            'shipped' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'cancellation_requested' => 0,
        ], $statusCounts);

        return response()->json(['statusCounts' => $statusCounts]);
    }

    public function approveCancellation(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                      ->where('status', 'cancellation_requested')
                      ->firstOrFail();

        DB::transaction(function() use ($order) {
            // Restore stock when cancelling
            $this->restoreStock($order);
            
            $order->update(['status' => 'cancelled']);

            // Log the change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'changed_at' => now(),
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'cancelled',
                'orderNumber' => $order->order_number,
                'message' => 'Cancellation approved and stock restored',
                'status_counts' => [
                    'pending'   => Order::where('status', 'pending')->count(),
                    'processing'=> Order::where('status', 'processing')->count(),
                    'shipped'   => Order::where('status', 'shipped')->count(),
                    'completed' => Order::where('status', 'completed')->count(),
                    'cancelled' => Order::where('status', 'cancelled')->count(),
                    'cancellation_requested' => Order::where('status', 'cancellation_requested')->count(),
                ],
            ]);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order #{$order->order_number} has been cancelled and stock restored");
    }

    /**
     * Reject cancellation request (Admin rejects user's cancellation)
     * NEW METHOD
     */
    public function rejectCancellation(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                      ->where('status', 'cancellation_requested')
                      ->firstOrFail();

        DB::transaction(function() use ($order) {
            // Revert to processing status
            $order->update(['status' => 'processing']);

            // Log the change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status' => 'processing',
                'changed_at' => now(),
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => 'processing',
                'orderNumber' => $order->order_number,
                'message' => 'Cancellation request rejected',
                'status_counts' => [
                    'pending'   => Order::where('status', 'pending')->count(),
                    'processing'=> Order::where('status', 'processing')->count(),
                    'shipped'   => Order::where('status', 'shipped')->count(),
                    'completed' => Order::where('status', 'completed')->count(),
                    'cancelled' => Order::where('status', 'cancelled')->count(),
                    'cancellation_requested' => Order::where('status', 'cancellation_requested')->count(),
                ],
            ]);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Cancellation request rejected for order #{$order->order_number}");
    }

    /**
     * Mark order as received (User action)
     */
    public function markReceived(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                      ->where('user_id', Auth::id())
                      ->where('status', 'shipped')
                      ->firstOrFail();

        $order->update(['status' => 'completed']);

        // Log the change
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status'   => 'completed',
            'changed_at' => now(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'orderNumber' => $order->order_number,
                'message' => 'Order marked as received successfully'
            ]);
        }

        return redirect()
            ->route('user.orders')
            ->with('success', 'Order marked as received successfully');
    }

    /**
     * Cancel order (User action - pending only)
     */
    public function cancelOrder(Request $request, $orderId)
    {
        $order = Order::where('id', $orderId)
                      ->where('user_id', Auth::id())
                      ->where('status', 'pending')
                      ->firstOrFail();

        DB::transaction(function() use ($order) {
            // Restore stock when cancelling
            $this->restoreStock($order);
            
            $order->update(['status' => 'cancelled']);

            // Log the change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status'   => 'cancelled',
                'changed_at' => now(),
            ]);
        });

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'orderNumber' => $order->order_number,
                'message' => 'Order cancelled successfully'
            ]);
        }

        return redirect()
            ->route('user.orders')
            ->with('success', 'Order cancelled successfully');
    }

    /**
     * Restore stock when order is cancelled
     */
    private function restoreStock(Order $order)
    {
        foreach ($order->items as $item) {
            $product = $item->product()->withTrashed()->first();
            
            if ($product && $item->size) {
                $size = strtolower($item->size);
                $stockField = 'stock_' . $size;
                
                // Validate size field exists
                if (in_array($size, ['s', 'm', 'l', 'xl', 'xxl']) && 
                    $product->getAttribute($stockField) !== null) {
                    $product->increment($stockField, $item->quantity);
                    
                    Log::info("Stock restored for product {$product->id}: +{$item->quantity} to {$stockField}");
                } else {
                    Log::warning("Invalid size or stock field for product {$product->id}: {$stockField}");
                }
            }
        }
    }

    /**
     * Deduct stock when cancelled order is restored
     */
    private function deductStock(Order $order)
    {
        foreach ($order->items as $item) {
            $product = $item->product()->withTrashed()->first();
            
            if ($product && $item->size) {
                $size = strtolower($item->size);
                $stockField = 'stock_' . $size;
                $currentStock = $product->getAttribute($stockField);

                // Validate size field exists
                if (in_array($size, ['s', 'm', 'l', 'xl', 'xxl']) && 
                    $currentStock !== null) {
                    
                    if ($currentStock >= $item->quantity) {
                        $product->decrement($stockField, $item->quantity);
                        
                        Log::info("Stock deducted for product {$product->id}: -{$item->quantity} from {$stockField}");
                    } else {
                        Log::warning("Insufficient stock for product {$product->id} (field: {$stockField}). Required: {$item->quantity}, Available: {$currentStock}");
                    }
                } else {
                    Log::warning("Invalid size or stock field for product {$product->id}: {$stockField}");
                }
            }
        }
    }
}