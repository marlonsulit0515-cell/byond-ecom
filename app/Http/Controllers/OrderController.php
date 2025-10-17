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
        
        $query = Order::with(['user', 'items', 'payment']);//fetch data of user's items and payment

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

        // Apply sorting - moved before pagination
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

        $orders = $query->paginate(15);

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
        ], $statusCounts);

        return view('AdminPanel.orders.index', compact('orders', 'statusCounts', 'allOrdersCount'));
    }


    /**
     * Display the specified order
     */
    public function show(Order $order)
    {
        $order->load(['user', 'items.product', 'payment']);

        return view('AdminPanel.orders.show', compact('order'));
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
            'notes' => 'nullable|string|max:500'
        ]);

        $oldStatus = $order->status;
        $newStatus = $request->status;

        // Handle stock logic
        if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
            $this->restoreStock($order);
        } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
            $this->deductStock($order);
        }

        $order->update(['status' => $newStatus]);

            // Log the change
        OrderStatusLog::create([
            'order_id' => $order->id,
            'status'   => $newStatus,
            'changed_at' => now(),
        ]);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'status' => $order->status,
                'oldStatus' => $oldStatus,
                'orderNumber' => $order->order_number,
                'new_status_label' => ucfirst($order->status),
                'status_counts' => [
                    'pending'   => Order::where('status', 'pending')->count(),
                    'processing'=> Order::where('status', 'processing')->count(),
                    'shipped'   => Order::where('status', 'shipped')->count(),
                    'completed' => Order::where('status', 'completed')->count(),
                    'cancelled' => Order::where('status', 'cancelled')->count(),
                ],
                'message' => "Order status updated to {$newStatus}"
            ]);
        }

        return redirect()
            ->route('orders.show', $order)
            ->with('success', "Order status updated to {$newStatus}");
    }

    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required|array',
            'order_ids.*' => 'exists:orders,id',
            'status' => 'required|in:pending,processing,shipped,completed,cancelled',
        ]);

        $orders = Order::whereIn('id', $request->order_ids)->get();
        $updatedOrders = [];

        foreach ($orders as $order) {
            $oldStatus = $order->status;
            $newStatus = $request->status;

            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                $this->restoreStock($order);
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                $this->deductStock($order);
            }

            $order->update(['status' => $newStatus]);

             // Log each order status change
            OrderStatusLog::create([
                'order_id' => $order->id,
                'status'   => $newStatus,
                'changed_at' => now(),
            ]);

            $updatedOrders[] = [
                'id' => $order->id,
                'status' => $order->status,
                'order_number' => $order->order_number,
                'oldStatus' => $oldStatus
            ];
        }

        // Return JSON for AJAX requests, redirect for regular requests
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'updatedCount' => count($updatedOrders),
                'updatedOrders' => $updatedOrders,
                'newStatus' => $request->status,
                'message' => count($request->order_ids) . " orders updated to {$request->status}"
            ]);
        }

        return redirect()
            ->route('orders.index')
            ->with('success', count($request->order_ids) . " orders updated to {$request->status}");
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
    ], $statusCounts);

    return response()->json(['statusCounts' => $statusCounts]);
}


public function markReceived(Request $request, $orderId)
{
    $order = Order::where('id', $orderId)
                  ->where('user_id', Auth::id()) // Ensure user owns the order
                  ->where('status', 'shipped') // Only shipped orders can be marked as received
                  ->firstOrFail();

    $order->update(['status' => 'completed']);

    

    // Optional: record status history
    /*
    $order->statusHistory()->create([
        'status' => 'completed',
        'notes' => 'Marked as received by customer',
        'updated_by' => auth()->id(),
    ]);
    */

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
 * Cancel order (pending -> cancelled)
 */
public function cancelOrder(Request $request, $orderId)
{
    $order = Order::where('id', $orderId)
                  ->where('user_id', Auth::id()) // Ensure user owns the order
                  ->where('status', 'pending') // Only pending orders can be cancelled
                  ->firstOrFail();

    // Restore stock when cancelling
    $this->restoreStock($order);
    
    $order->update(['status' => 'cancelled']);

    // Optional: record status history
    /*
    $order->statusHistory()->create([
        'status' => 'cancelled',
        'notes' => 'Cancelled by customer',
        'updated_by' => auth()->id(),
    ]);
    */

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
     * Export orders to CSV
     */
    public function export(Request $request)
    {
        $query = Order::with(['user', 'items', 'payment']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $filename = 'orders_' . now()->format('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        return response()->stream(function() use ($query) {
            $file = fopen('php://output', 'w');

            // CSV headers
            fputcsv($file, [
                'Order Number', 'Customer', 'Email', 'Phone', 'Status',
                'Total', 'Payment Method', 'Payment Status', 'Order Date'
            ]);

            // Use cursor to avoid memory issues
            foreach ($query->cursor() as $order) {
                fputcsv($file, [
                    $order->order_number,
                    $order->full_name,
                    $order->user->email ?? $order->guest_email,
                    $order->phone,
                    ucfirst($order->status),
                    '₱' . number_format((float) $order->total, 2),
                    ucfirst($order->payment->method ?? 'N/A'),
                    ucfirst($order->payment->status ?? 'N/A'),
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        }, 200, $headers);
    }

    /**
     * Restore stock when order is cancelled
     */
    private function restoreStock(Order $order)
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            if ($product && $item->size) {
                $stockField = 'stock_' . strtolower($item->size);
                if ($product->getAttribute($stockField) !== null) {
                    $product->increment($stockField, $item->quantity);
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
            $product = $item->product;
            if ($product && $item->size) {
                $stockField = 'stock_' . strtolower($item->size);
                $currentStock = $product->getAttribute($stockField);

                if ($currentStock !== null && $currentStock >= $item->quantity) {
                    $product->decrement($stockField, $item->quantity);
                } else {
                    // Optional: log warning if stock is insufficient
                    Log::warning("Stock deduction failed for product {$product->id} (field: {$stockField})");
                }
            }
        }
    }
}
