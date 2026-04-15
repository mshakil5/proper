<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlockedCustomer;
use Illuminate\Http\Request;
use DataTables;

class BlockedCustomerController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = BlockedCustomer::latest();

            return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('email', fn($row) => $row->email ?? '-')
            ->addColumn('domain', fn($row) => $row->domain ?? '-')
            ->addColumn('phone', fn($row) => $row->phone ?? '-')
            ->addColumn('reason', fn($row) => $row->reason ?? '-')
            ->addColumn('action', function ($row) {
                return '
                    <div class="dropdown">
                        <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown">
                            <i class="ri-more-fill"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                                <button class="dropdown-item editBtn" data-id="'.$row->id.'">
                                <i class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                    Edit
                                </button>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item" href="'.route('admin.blocked-orders.index', ['blocked_customer_id' => $row->id]).'">
                                    <i class="ri-shopping-cart-line align-bottom me-2 text-muted"></i> Orders
                                </a>
                            </li>
                            <li class="dropdown-divider"></li>
                            <li>
                                <button class="dropdown-item deleteBtn"
                                    data-delete-url="'.route('admin.blocked.destroy',$row->id).'"
                                    data-method="DELETE"
                                    data-table="#blockedTable">
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

        return view('admin.blocked.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'nullable|email',
            'domain' => 'nullable|string',
            'phone' => 'nullable|string',
            'reason' => 'nullable|string'
        ]);

        BlockedCustomer::create($request->all());

        return response()->json(['message' => 'Blocked added']);
    }

    public function edit($id)
    {
        return response()->json(BlockedCustomer::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $data = BlockedCustomer::findOrFail($id);
        $data->update($request->all());

        return response()->json(['message' => 'Updated']);
    }

    public function destroy($id)
    {
        BlockedCustomer::findOrFail($id)->delete();

        return response()->json(['message' => 'Deleted']);
    }
}