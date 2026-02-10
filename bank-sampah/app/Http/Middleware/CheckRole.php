<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Peran yang diizinkan (admin/nasabah)
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect('/login');
        }

        // 2. Cek apakah role user saat ini SESUAI dengan yang diminta route
        if (strtoupper(Auth::user()->role) == strtoupper($role)) {
            return $next($request);
        }

        // 3. Jika TIDAK SESUAI, lempar ke dashboard masing-masing (Smart Redirect)
        // Contoh: Nasabah coba buka link admin -> lempar ke dashboard nasabah
        if (Auth::user()->role == 'ADMIN') {
            return redirect('/admin/dashboard');
        } elseif (Auth::user()->role == 'NASABAH') {
            return redirect('/nasabah/dashboard');
        }

        // Default fallback (jika ada role aneh)
        return abort(403, 'Akses tidak diizinkan.');
    }
}
