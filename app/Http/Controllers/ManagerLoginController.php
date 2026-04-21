<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ManagerLoginController extends Controller
{
    /**
     * Tampilkan halaman login manajemen
     */
    public function showLoginForm()
    {
        // Kalau sudah login sebagai manajemen, langsung ke dashboard
        if (Auth::check() && Auth::user()->role === 'manajemen') {
            return redirect()->route('manager.dashboard');
        }

        return view('manager.auth.login');
    }

    /**
     * Proses login manajemen
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Cek apakah user yang login memang manajemen
            if (Auth::user()->role !== 'manajemen') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun ini tidak memiliki akses manajemen.',
                ])->onlyInput('email');
            }

            return redirect()->intended(route('manager.dashboard'));
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    /**
     * Logout manajemen
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('manager.login');
    }
}
