<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next, string ...$guards): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (auth()->guard($guard)->check()) {
                $user = auth()->guard($guard)->user();

                // Admin yang sudah login ke admin dashboard
                if ($user && $user->role === 'admin') {
                    return redirect()->route('admin.dashboard');
                }

                // User biasa ke /dashboard (Inertia → profile.edit)
                return redirect()->route('landing');
            }
        }

        return $next($request);
    }
}
