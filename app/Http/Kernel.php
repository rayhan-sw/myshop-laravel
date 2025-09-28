<?php

namespace App\Http;

use Illuminate\Foundation\Http\Kernel as HttpKernel;

class Kernel extends HttpKernel
{
    // … $middleware, $middlewareGroups, dll (biarkan)

    protected $middlewareAliases = [
        // bawaan Laravel…
        'auth'             => \App\Http\Middleware\Authenticate::class,
        'auth.basic'       => \Illuminate\Auth\Middleware\AuthenticateWithBasicAuth::class,
        'guest'            => \App\Http\Middleware\RedirectIfAuthenticated::class,
        'password.confirm' => \Illuminate\Auth\Middleware\RequirePassword::class,
        'signed'           => \Illuminate\Routing\Middleware\ValidateSignature::class,
        'throttle'         => \Illuminate\Routing\Middleware\ThrottleRequests::class,
        'verified'         => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        // >>> alias yang kamu pakai di web.php
        'block.admin.frontend' => \App\Http\Middleware\BlockAdminFromFrontend::class,
        'admin'                => \App\Http\Middleware\Admin::class,
    ];
}
