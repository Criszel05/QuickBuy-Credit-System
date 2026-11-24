<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $email = trim($request->email);

        // Find user account
        $user = User::where('email', $email)->first();

        // Check password
        $passwordMatches = $user
            ? Hash::check($request->password, $user->password)
            : false;

        // Temporary diagnostic log
        Log::info('QUICKBUY LOGIN CHECK', [
            'email' => $email,
            'database' => config('database.connections.mysql.database'),
            'user_found' => (bool) $user,
            'password_matches' => $passwordMatches,
            'is_active' => $user ? $user->is_active : null,
        ]);

        // Invalid credentials
        if (!$user || !$passwordMatches) {
            return back()
                ->withErrors([
                    'email' => 'Invalid email or password.'
                ])
                ->onlyInput('email');
        }

        // Check account status
        if (!$user->is_active) {
            return back()
                ->withErrors([
                    'email' => 'Your account is inactive.'
                ])
                ->onlyInput('email');
        }

        // Login user
        Auth::login($user);

        // Regenerate session
        $request->session()->regenerate();

        return redirect()->intended('/dashboard');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
