<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Belum login → arahkan ke login
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // Bukan admin → tolak
        if ($request->user()->role !== 'admin') {
            abort(403, 'Only admins can access this area.');
            // Atau: return redirect()->route('dashboard');
        }

        return $next($request);
    }
}
