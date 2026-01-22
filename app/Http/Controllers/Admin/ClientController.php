<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;
use Carbon\Carbon;
use App\Models\GiftCard;
use App\Models\UserPoint;
use App\Models\DeliverySubscription;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clients = User::where('user_type', 2)
                ->with(['orders', 'purchasedGiftCards', 'userPoints', 'deliverySubscription', 'deliverySubscriptionPayments'])
                ->latest();
            
            return DataTables::of($clients)
                ->addIndexColumn()
                ->addColumn('orders', function($row){
                    $ordersCount = $row->orders()->count();
                    return '<a href="'.route('admin.orders.index', ['client_id' => $row->id]).'" class="badge bg-primary">'.$ordersCount.'</a>';
                })
                ->addColumn('gift_cards', function($row){
                    $giftCardsCount = $row->purchasedGiftCards()->count();
                    return '<a href="'.route('gift-cards.index', ['client_id' => $row->id]).'" class="badge bg-info">'.$giftCardsCount.'</a>';
                })
                ->addColumn('points', function($row){
                    $pointsCount = $row->userPoints()->sum('point');
                    return '<a href="'.route('points.index', ['client_id' => $row->id]).'" class="badge bg-success">'.$pointsCount.'</a>';
                })
                ->addColumn('subscription', function($row){
                    $subscription = $row->deliverySubscription()->first();
                    $paymentsCount = $row->deliverySubscriptionPayments()->count();
                    
                    if ($subscription && $subscription->isActive()) {
                        return '<a href="'.route('subscriptions.index', ['client_id' => $row->id]).'" class="badge bg-warning">Active ('.$paymentsCount.')</a>';
                    }
                    return '<span class="badge bg-secondary">None</span>';
                })
                ->addColumn('status', function($row){
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="form-check form-switch" dir="ltr">
                                <input type="checkbox" class="form-check-input toggle-status" 
                                    id="customSwitchStatus'.$row->id.'" data-id="'.$row->id.'" '.$checked.'>
                                <label class="form-check-label" for="customSwitchStatus'.$row->id.'"></label>
                            </div>';
                })
                ->addColumn('action', function($row){
                    return '
                        <div class="dropdown">
                            <button class="btn btn-soft-secondary btn-sm" data-bs-toggle="dropdown"><i class="ri-more-fill"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><button class="dropdown-item EditBtn" data-id="'.$row->id.'"><i class="ri-pencil-fill me-2"></i>Edit</button></li>
                                <li class="dropdown-divider"></li>
                                <li><button class="dropdown-item deleteBtn" data-delete-url="'.route('client.destroy', $row->id).'"><i class="ri-delete-bin-fill me-2"></i>Delete</button></li>
                            </ul>
                        </div>';
                })
                ->rawColumns(['orders', 'gift_cards', 'points', 'subscription', 'status', 'action'])
                ->make(true);
        }

        return view('admin.clients.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date|before:today',
            'postcode' => 'nullable|string',
            'address_1' => 'nullable|string',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'address_2' => 'nullable|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'postcode' => $request->postcode,
            'address_1' => $request->address_1,
            'street' => $request->street,
            'city' => $request->city,
            'address_2' => $request->address_2,
            'password' => Hash::make($request->password),
            'user_type' => 2,
            'status' => 1,
        ]);

        return response()->json(['message' => 'Client created successfully.'], 201);
    }

    public function edit($id)
    {
        $client = User::findOrFail($id);
        return response()->json($client);
    }

    public function update(Request $request)
    {
        $rules = [
            'id' => 'required|exists:users,id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date|before:today',
            'postcode' => 'nullable|string',
            'address_1' => 'nullable|string',
            'street' => 'nullable|string',
            'city' => 'nullable|string',
            'address_2' => 'nullable|string',
        ];

        // Only validate password if provided
        if ($request->password) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $request->validate($rules);

        $client = User::findOrFail($request->id);
        
        $data = [
            'name' => $request->first_name . ' ' . $request->last_name,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'dob' => $request->dob,
            'postcode' => $request->postcode,
            'address_1' => $request->address_1,
            'street' => $request->street,
            'city' => $request->city,
            'address_2' => $request->address_2,
        ];

        // Only update password if provided
        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $client->update($data);

        return response()->json(['message' => 'Client updated successfully.'], 200);
    }

    public function destroy($id)
    {
        $client = User::findOrFail($id);
        $client->delete();
        return response()->json(['message' => 'Client deleted successfully.'], 200);
    }

    public function toggleStatus(Request $request)
    {
        $client = User::findOrFail($request->id);
        $client->status = $client->status == 1 ? 0 : 1;
        $client->save();
        return response()->json(['message' => 'Status updated successfully.'], 200);
    }

    public function exportClients()
    {
        $clients = User::where('user_type', 2)->select('first_name', 'last_name', 'email', 'phone', 'total_orders', 'last_order_date')->get();

        $filename = 'clients_' . date('Y-m-d_H-i-s') . '.csv';
        $handle = fopen('php://memory', 'w');

        fputcsv($handle, ['First Name', 'Last Name', 'Email', 'Telephone', 'Total Orders', 'Last Order Date']);

        foreach ($clients as $client) {
            fputcsv($handle, [
                $client->first_name,
                $client->last_name,
                $client->email,
                $client->phone,
                $client->total_orders,
                $client->last_order_date ? date('d-m-Y H:i', strtotime($client->last_order_date)) : ''
            ]);
        }

        fseek($handle, 0);
        return response()->stream(function() use ($handle) {
            fpassthru($handle);
        }, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ]);
    }

    public function importClients(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');

        $header = fgetcsv($handle);
        $updated = 0;
        $created = 0;
        $errors = [];

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) < 6) continue;

            $firstName = trim($row[0]);
            $lastName = trim($row[1]);
            $email = trim($row[2]);
            $phone = trim($row[3]);
            $totalOrders = (int)trim($row[4]);
            
            $lastOrderDate = null;
            $lastOrderValue = trim($row[5]);
            
            if ($lastOrderValue) {
                try {
                    $lastOrderDate = Carbon::createFromFormat('d-m-Y H:i', $lastOrderValue)->format('Y-m-d H:i:s');
                } catch (\Exception $e) {
                    try {
                        $lastOrderDate = Carbon::createFromFormat('Y-m-d H:i:s', $lastOrderValue)->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        $errors[] = "Invalid date format for {$email}: {$lastOrderValue}";
                        $lastOrderDate = null;
                    }
                }
            }

            $client = User::where('user_type', 2)->where('email', $email)->first();

            if ($client) {
                $client->update([
                    'name' => $firstName . ' ' . $lastName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $phone,
                    'total_orders' => $totalOrders,
                    'last_order_date' => $lastOrderDate
                ]);
                $updated++;
            } else {
                User::create([
                    'name' => $firstName . ' ' . $lastName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => $email,
                    'phone' => $phone,
                    'total_orders' => $totalOrders,
                    'last_order_date' => $lastOrderDate,
                    'user_type' => 2,
                    'status' => 1,
                    'password' => Hash::make('password123')
                ]);
                $created++;
            }
        }

        fclose($handle);

        return response()->json([
            'message' => "Created $created new clients and updated $updated existing clients successfully",
            'errors' => $errors
        ], 200);
    }

    public function giftCards(Request $request)
    {
        if ($request->ajax()) {
            $query = GiftCard::with('purchasedBy')->latest();

            if ($request->has('client_id') && $request->client_id) {
                $query->where('purchased_by', $request->client_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function($row){
                    return $row->purchasedBy->first_name . ' ' . $row->purchasedBy->last_name;
                })
                ->addColumn('amount', function($row){
                    return '£' . number_format($row->amount, 2);
                })
                ->addColumn('balance', function($row){
                    return '£' . number_format($row->balance, 2);
                })
                ->addColumn('created_at', function($row){
                    return $row->created_at->format('d F Y');
                })
                ->rawColumns(['amount', 'balance', 'created_at'])
                ->make(true);
        }

        return view('admin.gift-cards.index');
    }

    public function points(Request $request)
    {
        if ($request->ajax()) {
            $query = UserPoint::with('user')->latest();

            if ($request->has('client_id') && $request->client_id) {
                $query->where('user_id', $request->client_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function($row){
                    return $row->user->first_name . ' ' . $row->user->last_name;
                })
                ->addColumn('created_at', function($row){
                    return $row->created_at->format('d F Y');
                })
                ->rawColumns(['created_at'])
                ->make(true);
        }

        return view('admin.points.index');
    }

    public function subscriptions(Request $request)
    {
        if ($request->ajax()) {
            $query = DeliverySubscription::with(['user', 'payments'])->latest();

            if ($request->has('client_id') && $request->client_id) {
                $query->where('user_id', $request->client_id);
            }

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('client_name', function($row){
                    return $row->user->first_name . ' ' . $row->user->last_name;
                })
                ->addColumn('status', function($row){
                    $badgeClass = $row->status == 'active' ? 'bg-success' : 'bg-danger';
                    return '<span class="badge '.$badgeClass.'">'.$row->status.'</span>';
                })
                ->addColumn('payments', function($row){
                    $paymentsCount = $row->payments->count();
                    return '<span class="badge bg-info">'.$paymentsCount.'</span>';
                })
                ->addColumn('started_at', function($row){
                    return $row->started_at->format('d F Y');
                })
                ->addColumn('ends_at', function($row){
                    return $row->ends_at->format('d F Y');
                })
                ->rawColumns(['status', 'payments', 'started_at', 'ends_at'])
                ->make(true);
        }

        return view('admin.subscriptions.index');
    }
}