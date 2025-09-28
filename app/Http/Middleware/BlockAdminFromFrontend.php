<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class BlockAdminFromFrontend
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Admin yang sudah login tidak boleh melihat halaman frontend publik
        if ($user && ($user->role === 'admin' || (($user->is_admin ?? false) === true))) {
            return redirect()->route('admin.dashboard');
        }

        return $next($request);
    }
}
