<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\WelcomeMail;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = '/user/dashboard';

    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $this->validator($request->all())->validate();

        $existingUser = User::where('email', strtolower($validated['email']))->first();

        if ($existingUser) {
            if (Hash::check($validated['password'], $existingUser->password)) {
                Auth::login($existingUser);
                $request->session()->regenerate();
                $existingUser->update(['last_login' => now()]);

                if ($request->redirect_to_checkout) {
                    return redirect('/checkout');
                }

                return redirect($this->redirectTo);
            } else {
                return back()->withInput($request->only('email'))
                    ->withErrors(['password' => 'Password is incorrect']);
            }
        }

        $user = $this->create($validated);

        Auth::login($user);

        $request->session()->regenerate();

        try {
            Mail::to($user->email)->send(new WelcomeMail($user));
        } catch (Exception $e) {
        }

        if ($request->redirect_to_checkout) {
            return redirect('/checkout');
        }

        return redirect($this->redirectTo);
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'last_name' => ['required', 'string', 'max:255', 'regex:/^[a-zA-Z\s\'-]+$/'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255'],
            'phone' => ['required', 'string', 'regex:/^(?:\+44\s?|0)[0-9\s]{9,11}$/'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'terms' => ['required', 'accepted']
        ], [
            'first_name.required' => 'First name is required',
            'first_name.string' => 'First name must be a valid text',
            'first_name.max' => 'First name cannot exceed 255 characters',
            'first_name.regex' => 'First name can only contain letters, spaces, hyphens and apostrophes',
            
            'last_name.required' => 'Last name is required',
            'last_name.string' => 'Last name must be a valid text',
            'last_name.max' => 'Last name cannot exceed 255 characters',
            'last_name.regex' => 'Last name can only contain letters, spaces, hyphens and apostrophes',
            
            'email.required' => 'Email address is required',
            'email.email' => 'Please enter a valid email address',
            'email.max' => 'Email address cannot exceed 255 characters',
            
            'phone.required' => 'UK phone number is required',
            'phone.regex' => 'Please enter a valid UK phone number',
            'phone.string' => 'Phone number must be valid',
            
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters long',
            'password.confirmed' => 'Passwords do not match',
            'password.regex' => 'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character (@$!%*?&)',
            
            'terms.required' => 'You must accept the terms and conditions',
            'terms.accepted' => 'You must accept the terms and conditions'
        ]);
    }

    protected function create(array $data)
    {
        $fullName = $data['first_name'] . ' ' . $data['last_name'];
        
        $phone = preg_replace('/\s+/', '', $data['phone']);

        return User::create([
            'name' => $fullName,
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => strtolower($data['email']),
            'phone' => $phone,
            'password' => Hash::make($data['password']),
            'user_type' => '2',
            'status' => 1,
            'image' => '/placeholder.webp',
            'last_login' => now()
        ]);
    }

    public function login(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:6',
        ], [
            'email.required' => 'Email is required',
            'email.email' => 'Please enter a valid email',
            'password.required' => 'Password is required',
            'password.min' => 'Password must be at least 6 characters'
        ]);

        $user = User::where('email', $validated['email'])->first();

        if (!$user) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Email not found']);
        }

        if ($user->status != 1) {
            return back()->withInput($request->only('email'))
                ->withErrors(['email' => 'Your account is inactive']);
        }

        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']], $request->remember)) {
            $request->session()->regenerate();

            $user->update(['last_login' => now()]);

            if ($request->redirect_to_checkout) {
                return redirect('/checkout');
            }

            if (auth()->user()->user_type == '1') {
                return redirect()->route('admin.dashboard');
            } else {
                return redirect()->route('user.dashboard');
            }
        }

        return back()->withInput($request->only('email'))
            ->withErrors(['password' => 'Password is incorrect']);
    }
}