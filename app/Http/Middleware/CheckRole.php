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
     * @param  string  ...$roles  Array of allowed roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) { // Jika pengguna tidak login
            return redirect()->route('login'); // Arahkan ke halaman login
        }

        $user = Auth::user();

        // Superadmin bisa akses semua
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        // Cek apakah peran pengguna ada di dalam daftar peran yang diizinkan
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // Jika tidak memiliki peran yang sesuai, kembalikan error 403 (Forbidden)
        // atau arahkan ke halaman lain
        abort(403, 'ANDA TIDAK MEMILIKI AKSES UNTUK HALAMAN INI.'); 
        // return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses halaman tersebut.');
    }
}
