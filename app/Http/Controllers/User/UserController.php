<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function profile()
    {
        return view('user.profile');
    }

    public function password()
    {
        return view('user.password');
    }

    public function orders()
    {
        return view('user.orders');
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
