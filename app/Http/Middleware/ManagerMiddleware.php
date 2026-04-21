<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ManagerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('manager.login');
        }

        // Cek apakah user memiliki role 'manajemen'
        if (Auth::user()->role !== 'manajemen') {
            Auth::logout();
            return redirect()->route('manager.login')->withErrors([
                'email' => 'Anda tidak memiliki akses ke halaman ini.'
            ]);
        }

        return $next($request);
    }
}
