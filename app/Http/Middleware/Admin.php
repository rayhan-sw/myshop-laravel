<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    // Middleware: membatasi akses hanya untuk admin
    public function handle(Request $request, Closure $next): Response
    {
        // Jika belum login  arahkan ke halaman login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Jika sudah login tapi bukan admin  tolak akses
        if ($request->user()->role !== 'admin') {
            abort(403, 'Only admins can access this area.');
        }

        return $next($request);
    }
}
