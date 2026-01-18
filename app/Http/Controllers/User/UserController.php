<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\Order;

class UserController extends Controller
{
    public function profile()
    {
        $user = auth()->user();
        return view('user.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . auth()->id(),
            'phone' => 'required|string|max:20',
            'dob' => 'nullable|date|before:today',
            'postcode' => 'nullable|string',
        ]);

        auth()->user()->update($validated);

        return response()->json(['message' => 'Profile updated successfully']);
    }

    public function password()
    {
        return view('user.password');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|string|min:6|confirmed',
        ]);

        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect'
            ], 422);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'message' => 'Password updated successfully!'
        ]);
    }

    public function orders()
    {
        return view('user.orders');
    }

    public function orderDetails(Order $order)
    {
        if ($order->user_id !== auth()->id()) {
            abort(403);
        }

        return view('user.order-details', compact('order'));
    }

    public function coupons()
    {
        return view('user.coupons');
    }

    public function points()
    {
        return view('user.points');
    }

    public function social()
    {
        return view('user.social');
    }
}
