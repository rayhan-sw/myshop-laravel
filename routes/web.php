<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\BlockAdminFromFrontend;

// ===== Frontend (Inertia) =====
// Admin TIDAK boleh masuk ke grup ini → diblokir oleh middleware FQCN
/*
Route::middleware(BlockAdminFromFrontend::class)->group(function () {
    Route::get('/', fn () => Inertia::render('Landing'))->name('landing');

    Route::get('/shop',        fn () => Inertia::render('Shop'))       ->name('shop');
    Route::get('/contact',     fn () => Inertia::render('Contact'))    ->name('contact');
    Route::get('/testimonial', fn () => Inertia::render('Testimonial'))->name('testimonial');
    Route::get('/why',         fn () => Inertia::render('Why'))        ->name('why');
});
*/
Route::get('/', fn () => Inertia::render('Landing'))->name('landing');
    Route::get('/shop',        fn () => Inertia::render('Shop'))       ->name('shop');
    Route::get('/contact',     fn () => Inertia::render('Contact'))    ->name('contact');
    Route::get('/testimonial', fn () => Inertia::render('Testimonial'))->name('testimonial');
    Route::get('/why',         fn () => Inertia::render('Why'))        ->name('why');

// ===== Dashboard universal =====
// - Admin  → redirect ke admin.dashboard (Blade)
// - User   → render Inertia 'Dashboard' (pastikan Pages/Dashboard.vue ada)
Route::get('/dashboard', function () {
    $u = auth()->user();

    if ($u && ($u->role === 'admin' || (($u->is_admin ?? false) === true))) {
        return redirect()->route('admin.dashboard');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// ===== User profile (Inertia) =====
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ===== Admin (Blade) =====
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','verified','admin'])
    ->group(function () {
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // ===== Categories =====
        Route::get('/categories',                [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories',               [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',     [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}',  [CategoryController::class, 'destroy'])->name('categories.destroy');

        // ===== Products =====
        Route::get('/products',                  [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',           [ProductController::class, 'create'])->name('products.create');
        Route::post('/products',                 [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit',   [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',        [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',     [ProductController::class, 'destroy'])->name('products.destroy');
    });

// Auth (Breeze)
require __DIR__ . '/auth.php';
