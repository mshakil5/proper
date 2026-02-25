<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $query = Order::with(['user', 'payment'])->latest();

            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('client_id') && $request->client_id) {
                $query->where('user_id', $request->client_id);
            }

            if ($request->has('customer') && $request->customer) {
                $query->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$request->customer}%"]);
            }

            if ($request->has('start_date') && $request->start_date) {
                $query->whereDate('created_at', '>=', $request->start_date);
            }

            if ($request->has('end_date') && $request->end_date) {
                $query->whereDate('created_at', '<=', $request->end_date);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('order_number', function ($row) {
                    return '#' . $row->order_number;
                })
                ->addColumn('customer', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->addColumn('amounts', function ($row) {
                    return '£' . number_format($row->total, 2);
                })
                ->addColumn('order_status', function ($row) {
                    $statusColors = [
                        'pending' => 'warning',
                        'confirmed' => 'info',
                        'preparing' => 'primary',
                        'ready' => 'info',
                        'out_for_delivery' => 'secondary',
                        'delivered' => 'success',
                        'cancelled' => 'danger'
                    ];
                    $color = $statusColors[$row->status] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst(str_replace('_', ' ', $row->status)) . '</span>';
                })
                ->addColumn('payment_status', function ($row) {
                    $status = $row->payment?->status ?? 'pending';
                    $badge  = $row->payment?->status_badge ?? 'warning';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('delivery_type', function ($row) {
                    $colors = [
                        'delivery'   => 'primary',
                        'collection' => 'info',
                    ];
                    $type  = $row->delivery_type ?? 'N/A';
                    $color = $colors[$type] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($type) . '</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('M d, Y g:i A');
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('admin.orders.details', $row->id) . '" class="btn btn-sm btn-primary">
                        <i class="ri-eye-line"></i> View
                    </a>';
                })
                ->rawColumns(['order_status', 'payment_status', 'action', 'delivery_type'])
                ->make(true);
        }

        return view('admin.orders.index');
    }

    public function show(Order $order)
    {
        $order->load(['items.options', 'user', 'payment']);
        return view('admin.orders.details', compact('order'));
    }
}
