<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedCustomer;
use App\Models\BlockedCustomerOrder;
use App\Models\Product;
use DataTables;
use Illuminate\Http\Request;

class BlockedCustomerOrderController extends Controller
{
    public function index(Request $request)
    {
        $blockedCustomerId = $request->query('blocked_customer_id');

        if ($request->ajax()) {
            $query = BlockedCustomerOrder::with('blockedCustomer')->latest();

            if ($blockedCustomerId) {
                $query->where('blocked_customer_id', $blockedCustomerId);
            }

            $data = $query;

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('email', fn($row) => $row->email ?? '-')
            ->addColumn('phone', fn($row) => $row->phone ?? '-')
            ->addColumn('blocked_customer', fn($row) => $row->blockedCustomer?->email ?? '-')
            ->addColumn('total_items', function($row) {
                $cart = $row->order_data['cart'] ?? [];
                return count($cart);
            })
            ->addColumn('total', function($row) {
                return '£' . number_format($row->order_data['summary']['total'] ?? 0, 2);
            })
            ->addColumn('date', fn($row) => $row->created_at->format('d M Y H:i'))
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown">
                            <i class="ri-more-fill"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item viewBtn" data-id="'.$row->id.'">
                                <i class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                    View
                                </button>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item deleteBtn"
                                    data-delete-url="'.route('admin.blocked-orders.destroy',$row->id).'"
                                    data-method="DELETE"
                                    data-table="#blockedOrdersTable">
                                <i class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                    Delete
                                </button>
                            </li>
                        </ul>
                    </div>
                ';
            })
            ->rawColumns(['action'])
            ->make(true);
        }

        $blockedCustomer = null;
        if ($blockedCustomerId) {
            $blockedCustomer = BlockedCustomer::find($blockedCustomerId);
        }

        return view('admin.blocked.orders', compact('blockedCustomer', 'blockedCustomerId'));
    }

    public function show($id)
    {
        $blockedOrder = BlockedCustomerOrder::with('blockedCustomer')->findOrFail($id);

        $orderData = $blockedOrder->order_data ?? [];
        $cart = $orderData['cart'] ?? [];

        foreach ($cart as &$item) {
            $product = Product::find($item['productId']);
            $item['name'] = $product->title ?? 'Unknown Product';
        }

        return response()->json([
            'id' => $blockedOrder->id,
            'email' => $blockedOrder->email,
            'phone' => $blockedOrder->phone,
            'blocked_customer_email' => $blockedOrder->blockedCustomer?->email,
            'created_at' => $blockedOrder->created_at->format('d M Y H:i:s'),

            'cart' => $cart,
            'summary' => $orderData['summary'] ?? ['total' => 0],
            'customer' => $orderData['customer'] ?? null,
            'delivery' => $orderData['delivery'] ?? null,
        ]);
    }

    public function destroy($id)
    {
        BlockedCustomerOrder::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}