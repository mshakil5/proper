<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Product;
use App\Models\Contact;

class HomeController extends Controller
{
    public function dashboard()
    { 
        if (Auth::check()) {
            $user = auth()->user();

            if ($user->is_type == '1') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->is_type == '2') {
                return redirect()->route('manager.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        } else {
            return redirect()->route('login');
        }
    }
    
    public function adminHome()
    {
        $productsCount = Product::all()->count();
        $unreadMessagesCount = Contact::where('status', 0)->count();
        return view('admin.dashboard', compact('productsCount', 'unreadMessagesCount'));
    }

    public function managerHome()
    {
        return view('home');
    }

    public function userHome()
    {
        return view('home');
    }
}
