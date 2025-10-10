<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ShopController; // untuk /shop (filtering)

// Models (dipakai di closure Home)
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

// use App\Http\Middleware\BlockAdminFromFrontend;

/*
|--------------------------------------------------------------------------
| Frontend (Inertia)
|--------------------------------------------------------------------------
| Home (/) tetap ada. Kita menambahkan data roots & latestProducts.
*/
Route::get('/', function () {
    $roots = Category::with(['children' => fn($q) => $q->orderBy('name')])
        ->whereNull('parent_id')
        ->orderBy('name')
        ->get(['id','name','parent_id','created_at']);

    $latestProducts = Product::with(['category.parent','images'])
        ->when(Schema::hasColumn('products','is_active'), fn($q) => $q->where('is_active', true))
        ->latest()
        ->take(8)
        ->get();

    return Inertia::render('Landing', [
        'roots'          => $roots,
        'latestProducts' => $latestProducts,
    ]);
})->name('landing');

// Shop (filter q / root_id / sub_id)
Route::get('/shop', [ShopController::class, 'shop'])->name('shop');

// >>> Route baru: detail produk
Route::get('/product/{product}', [ShopController::class, 'show'])->name('product.show');

Route::get('/contact',     fn () => Inertia::render('Contact'))    ->name('contact');
Route::get('/testimonial', fn () => Inertia::render('Testimonial'))->name('testimonial');
Route::get('/why',         fn () => Inertia::render('Why'))        ->name('why');

/*
|--------------------------------------------------------------------------
| Dashboard universal
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    $u = auth()->user();

    if ($u && ($u->role === 'admin' || (($u->is_admin ?? false) === true))) {
        return redirect()->route('admin.dashboard');
    }

    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| User profile (Inertia)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])  ->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin (Blade)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','verified','admin'])
    ->group(function () {

        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // ===== Categories (root & sub; "masuk parent dulu") =====
        Route::get('/categories',               [CategoryController::class, 'index'])->name('categories.index'); // list ROOT
        Route::get('/categories/{category}',    [CategoryController::class, 'show']) ->name('categories.show');  // manage SUB dr ROOT tsb
        Route::post('/categories',              [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',    [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // ===== Products =====
        Route::get('/products',                 [ProductController::class, 'index'])   ->name('products.index');
        Route::get('/products/create',          [ProductController::class, 'create'])  ->name('products.create');
        Route::post('/products',                [ProductController::class, 'store'])   ->name('products.store');
        Route::get('/products/{product}/edit',  [ProductController::class, 'edit'])    ->name('products.edit');
        Route::put('/products/{product}',       [ProductController::class, 'update'])  ->name('products.update');
        Route::delete('/products/{product}',    [ProductController::class, 'destroy']) ->name('products.destroy');
    });

// Auth (Breeze)
require __DIR__ . '/auth.php';
