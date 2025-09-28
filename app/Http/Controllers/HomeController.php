<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class HomeController extends Controller
{
    /**
     * Halaman dashboard admin
     */
    public function index(): Response
    {
        // Render file resources/js/Pages/Admin/Index.vue
        return Inertia::render('Admin/Index');
    }
}
