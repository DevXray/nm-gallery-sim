<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestSession
{
    /**
     * Redirect ke dashboard jika user sudah login.
     * Middleware 'guest' default Laravel tidak akan bekerja
     * karena auth sistem ini berbasis session manual.
     */
    public function handle(Request $request, Closure $next)
    {
        if (session()->has('user')) {
            $role = session('user')['role'] ?? 'Owner';
            if ($role === 'Karyawan') {
                return redirect()->route('transaksi.index');
            }
            return redirect()->route('dashboard');
        }
        return $next($request);
    }
}
