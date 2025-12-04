<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Subscription;

class SubscriptionController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Subscription::with('plan', 'user')->latest();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('plan', fn($row) => $row->plan ? $row->plan->name : '-')
                ->addColumn('user', fn($row) => $row->user ? $row->user->name : 'Guest')
                ->addColumn('email', fn($row) => $row->user ? $row->user->email : $row->email ?? '-')
                ->addColumn('payment_status', fn($row) => $row->payment_status)
                ->addColumn('action', function($row) {
                    // Each row has its own modal
                    return '
                    <button class="btn btn-sm btn-info" data-toggle="modal" data-target="#subscriptionModal'.$row->id.'">
                        <i class="fas fa-eye"></i>
                    </button>

                    <!-- Modal -->
                    <div class="modal fade" id="subscriptionModal'.$row->id.'" tabindex="-1" role="dialog">
                      <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                          <div class="modal-header">
                            <h5 class="modal-title">Subscription Details</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                              <span aria-hidden="true">&times;</span>
                            </button>
                          </div>
                          <div class="modal-body">
                            <ul style="list-style:none; padding:0;">
                                <li><strong>Name:</strong> '.($row->user?->name ?? 'Guest').'</li>
                                <li><strong>Email:</strong> '.($row->user?->email ?? $row->email ?? '-').'</li>
                                <li><strong>Plan:</strong> '.($row->plan?->name ?? '-').'</li>
                                <li><strong>Amount:</strong> £'.$row->amount.'</li>
                                <li><strong>Status:</strong> '.$row->payment_status.'</li>
                                <li><strong>Phone:</strong> '.$row->phone.'</li>
                                <li><strong>Country:</strong> '.$row->country.'</li>
                                <li><strong>City:</strong> '.$row->city.'</li>
                                <li><strong>Address Line1:</strong> '.$row->line1.'</li>
                                <li><strong>Address Line2:</strong> '.$row->line2.'</li>
                                <li><strong>Postal Code:</strong> '.$row->postal_code.'</li>
                            </ul>
                          </div>
                        </div>
                      </div>
                    </div>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('admin.subscriptions.index');
    }
}
