<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $role  Peran yang diizinkan (admin/nasabah)
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Cek apakah user sudah login
        if (! Auth::check()) {
            return redirect('/login');
        }

        $userRole = strtoupper(Auth::user()->role);

        // 2. Cek apakah role user saat ini SESUAI dengan salah satu yang diminta route
        foreach ($roles as $r) {
            if ($userRole === strtoupper(trim($r))) {
                return $next($request);
            }
        }

        // 3. Jika TIDAK SESUAI, lempar ke dashboard masing-masing (Smart Redirect)
        if ($userRole === 'ADMIN') {
            if (! $request->is('admin/dashboard') && ! $request->is('admin')) {
                return redirect('/admin/dashboard');
            }
        } elseif ($userRole === 'NASABAH') {
            if (! $request->is('nasabah/dashboard') && ! $request->is('nasabah')) {
                return redirect('/nasabah/dashboard');
            }
        }

        // Default fallback (jika ada role aneh atau sudah berada di dashboard yang sesuai tapi ditolak)
        return abort(403, 'Akses tidak diizinkan.');
    }
}
