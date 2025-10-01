<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Middleware\BlockAdminFromFrontend; 

// ===== Frontend (Inertia) =====
// Pakai FQCN middleware, bukan alias string
Route::middleware(BlockAdminFromFrontend::class)->group(function () {
    Route::get('/', fn () => Inertia::render('Landing'))->name('landing');
    Route::get('/shop',        fn () => Inertia::render('Shop'))       ->name('shop');
    Route::get('/contact',     fn () => Inertia::render('Contact'))    ->name('contact');
    Route::get('/testimonial', fn () => Inertia::render('Testimonial'))->name('testimonial');
    Route::get('/why',         fn () => Inertia::render('Why'))        ->name('why');
});

// Dashboard universal
Route::get('/dashboard', function () {
    $u = auth()->user();
    if ($u && ($u->role === 'admin' || (($u->is_admin ?? false) === true))) {
        return redirect()->route('admin.dashboard');
    }
    return inertia::render('dashboard');
})->middleware(['auth','verified'])->name('landing');

// User profile
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
        Route::get('/categories',               [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories',              [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',    [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    });

require __DIR__ . '/auth.php';
