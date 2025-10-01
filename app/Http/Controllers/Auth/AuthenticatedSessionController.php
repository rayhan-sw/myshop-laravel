<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status'           => session('status'),
        ]);
    }

    public function store(LoginRequest $request): RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        $request->authenticate();
        $request->session()->regenerate();

        $user = $request->user();

        // IMPORTANT: admin must hard-redirect to Blade admin
        if ($user && $user->role === 'admin') {
            return Inertia::location(route('admin.dashboard'));  // <— this is the key
        }

        // Normal users continue in SPA
        return redirect()->intended(route('landing'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Go back to SPA landing after logout
        return redirect()->route('landing');
    }
}
