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
            if ($request->has('type') && $request->type) {
                $query->where('order_type', $request->type);
            }

            if ($request->has('client_id') && $request->client_id) {
                $query->where('user_id', $request->client_id);
            }

            if ($request->has('order_type') && $request->order_type && $request->order_type !== 'all') {
                $query->where('order_type', $request->order_type);
            }

            if ($request->has('payment_method') && $request->payment_method) {
                $query->where('payment_method', $request->payment_method);
            }
            $searchValue = null;

            if ($request->has('search') && !empty($request->search['value'])) {
                $searchValue = trim($request->search['value']);
            }
            elseif ($request->filled('order_number')) {
                $searchValue = trim($request->order_number);
            }
            elseif ($request->filled('customer')) {
                $searchValue = trim($request->customer);
            }

            if ($searchValue) {
                $clean = str_replace('#', '', $searchValue);

                $query->where(function ($q) use ($clean, $searchValue) {
                    $q->where('order_number', 'LIKE', "%{$clean}%")
                      ->orWhere('order_number', 'LIKE', "%{$searchValue}%")
                      ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%{$searchValue}%"])
                      ->orWhere('email', 'LIKE', "%{$searchValue}%")
                      ->orWhere('phone', 'LIKE', "%{$searchValue}%");
                });
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
                    return '<strong>#' . $row->order_number . '</strong>';
                })
                ->addColumn('customer', function ($row) {
                    $type = $row->user_id 
                        ? '<span class="badge bg-success">Registered</span>' 
                        : '<span class="badge bg-secondary">Guest</span>';
                    return $row->first_name . ' ' . $row->last_name . 
                        '<br><small class="text-muted"><i class="ri-mail-line"></i> ' . $row->email . '</small>' .
                        '<br><small class="text-muted"><i class="ri-phone-line"></i> ' . ($row->phone ?? 'N/A') . '</small>' .
                        '<br>' . $type;
                })
                ->addColumn('order_type', function ($row) {
                    $label = $row->order_type === 'pos' ? 'POS Sale' : 'Online Sale';
                    $color = $row->order_type === 'pos' ? 'warning' : 'primary';
                    return '<span class="badge bg-' . $color . '">' . $label . '</span>';
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
                    if ($row->payment_method == 'cash') {
                        if ($row->status == 'delivered') {
                            return '<span class="badge bg-success">Completed</span>';
                        }
                        return '<span class="badge bg-warning">Pending (Cash)</span>';
                    }
                    $status = $row->payment?->status ?? 'pending';
                    $badge  = $row->payment?->status_badge ?? 'warning';
                    return '<span class="badge bg-' . $badge . '">' . ucfirst($status) . '</span>';
                })
                ->addColumn('delivery_type', function ($row) {
                    $colors = ['delivery' => 'primary', 'collection' => 'info'];
                    $type  = $row->delivery_type ?? 'N/A';
                    $color = $colors[$type] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($type) . '</span>';
                })
                ->addColumn('payment_method', function ($row) {
                    $colors = ['cash' => 'success', 'stripe' => 'info', 'paypal' => 'warning'];
                    $method = $row->payment_method ?? 'N/A';
                    $color  = $colors[$method] ?? 'secondary';
                    return '<span class="badge bg-' . $color . '">' . ucfirst($method) . '</span>';
                })
                ->addColumn('date', function ($row) {
                    return $row->created_at->format('M d, Y g:i A');
                })
                ->addColumn('action', function ($row) {
                    return '
                        <div class="d-flex flex-column gap-1">
                            <a href="' . route('admin.pos.receipt', $row->id) . '" 
                            target="_blank"
                            class="btn btn-sm btn-dark">
                                <i class="ri-printer-line"></i> Print
                            </a>

                            <a href="' . route('admin.orders.details', $row->id) . '" 
                            class="btn btn-sm btn-primary">
                                <i class="ri-eye-line"></i> View
                            </a>
                        </div>
                    ';
                })
                ->rawColumns(['order_number', 'customer', 'order_type', 'order_status', 'payment_status', 'delivery_type', 'payment_method', 'action'])
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