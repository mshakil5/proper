<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class ClientController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $clients = User::where('user_type', 2)->latest();
            return DataTables::of($clients)
                ->addIndexColumn()
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
                ->rawColumns(['status', 'action'])
                ->make(true);
        }

        return view('admin.clients.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
        ];

        // Only validate password if provided
        if ($request->password) {
            $rules['password'] = 'required|string|min:6|confirmed';
        }

        $request->validate($rules);

        $client = User::findOrFail($request->id);
        
        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
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
}