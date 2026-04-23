<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user sesuai dengan yang diminta di Route
        if (Auth::user()->role !== $role) {
            // Jika tidak sesuai (misal Customer coba masuk URL Admin), lemparkan ke halaman 403 (Forbidden)
            abort(403, 'Akses Ditolak. Anda tidak memiliki izin ke halaman ini.');
        }

        return $next($request);
    }
}