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

            if ($user->user_type == '1') {
                return redirect()->route('admin.dashboard');
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
        return view('admin.pages.dashboard', compact('productsCount', 'unreadMessagesCount'));
    }

    public function userHome()
    {
        return view('home');
    }
}
