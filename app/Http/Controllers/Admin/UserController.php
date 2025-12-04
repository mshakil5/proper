<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $users = User::where('is_type', 3)->latest()->get();
            return DataTables::of($users)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    $checked = $row->status == 1 ? 'checked' : '';
                    return '<div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input toggle-status" id="customSwitchStatus' . $row->id . '" data-id="' . $row->id . '" ' . $checked . '>
                                <label class="custom-control-label" for="customSwitchStatus' . $row->id . '"></label>
                            </div>';
                })
                ->addColumn('action', function($row){
                    $editBtn = '<button class="btn btn-sm btn-info edit" data-id="'.$row->id.'"><i class="fas fa-edit"></i></button>';
                    $deleteBtn = '<button class="btn btn-sm btn-danger delete" data-id="'.$row->id.'"><i class="fas fa-trash-alt"></i></button>';
                    return $editBtn.' '.$deleteBtn;
                })
                ->rawColumns(['status','action'])
                ->make(true);
        }

        return view('admin.users.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:20',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
            'is_type' => 3,
            'status' => 1,
        ]);

        return response()->json(['status' => 200, 'message' => 'User created successfully', 'user' => $user]);
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:users,id',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $request->id,
            'phone' => 'required|string|max:20',
            'password' => 'nullable|string|min:6|confirmed',
        ]);

        $user = User::findOrFail($request->id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(['status' => 200, 'message' => 'User deleted successfully']);
    }

    public function toggleStatus(Request $request)
    {
        $user = User::findOrFail($request->id);
        $user->status = $user->status == 1 ? 0 : 1;
        $user->save();
        return response()->json(['status' => 200, 'message' => 'Status updated', 'new_status' => $user->status]);
    }
}