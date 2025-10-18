<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\ShopController;   // Halaman shop/filter produk
use App\Http\Controllers\CartController;   // Keranjang belanja user
use App\Http\Controllers\OrderController;  // Transaksi & checkout

// Models (dipakai di halaman utama)
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

// Halaman utama (Landing Page)
Route::get('/', function () {
    // Ambil kategori utama beserta subkategori (anak)
    $roots = Category::with(['children' => fn($q) => $q->orderBy('name')])
        ->whereNull('parent_id')
        ->orderBy('name')
        ->get(['id','name','parent_id','created_at']);

    // Ambil 8 produk terbaru yang aktif (jika kolom is_active ada)
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

// Halaman katalog dan detail produk
Route::get('/shop', [ShopController::class, 'shop'])->name('shop');
Route::get('/product/{product:slug}', [ShopController::class, 'show'])->name('product.show');

// Halaman statis tambahan
Route::get('/contact', fn() => Inertia::render('Contact'))->name('contact');
Route::get('/testimonial', fn() => Inertia::render('Testimonial'))->name('testimonial');
Route::get('/why', fn() => Inertia::render('Why'))->name('why');

// Dashboard universal (user vs admin)
Route::get('/dashboard', function () {
    $u = auth()->user();

    // Jika admin, arahkan ke dashboard admin
    if ($u && ($u->role === 'admin' || (($u->is_admin ?? false) === true))) {
        return redirect()->route('admin.dashboard');
    }

    // Jika bukan admin, tampilkan dashboard default (Inertia)
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Profil pengguna (Inertia)
Route::middleware('auth')->group(function () {
    Route::get('/profile',   [ProfileController::class, 'edit'])->name('profile.edit');     // Edit profil
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update'); // Simpan perubahan
    Route::delete('/profile',[ProfileController::class, 'destroy'])->name('profile.destroy'); // Hapus akun
});

// Cart & Orders (User yang login dan terverifikasi)
Route::middleware(['auth','verified'])->group(function () {
    // Keranjang belanja
    Route::get('/cart',                 [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/items',          [CartController::class, 'store'])->name('cart.items.store');
    Route::patch('/cart/items/{item}',  [CartController::class, 'update'])->name('cart.items.update');
    Route::delete('/cart/items/{item}', [CartController::class, 'destroy'])->name('cart.items.destroy');
    Route::delete('/cart/clear',        [CartController::class, 'clear'])->name('cart.clear');

    // Checkout dan riwayat pesanan user
    Route::get('/checkout',  [OrderController::class, 'checkoutForm'])->name('checkout.form');
    Route::post('/checkout', [OrderController::class, 'store'])->name('checkout.store');
    Route::get('/orders',    [OrderController::class, 'index'])->name('orders.index');
});

// Bagian admin (Blade)
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth','verified','admin'])
    ->group(function () {
        // Dashboard admin
        Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

        // Manajemen kategori
        Route::get('/categories',               [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/{category}',    [CategoryController::class, 'show'])->name('categories.show');
        Route::post('/categories',              [CategoryController::class, 'store'])->name('categories.store');
        Route::put('/categories/{category}',    [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        // Manajemen produk
        Route::get('/products',                 [ProductController::class, 'index'])->name('products.index');
        Route::get('/products/create',          [ProductController::class, 'create'])->name('products.create');
        Route::post('/products',                [ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}/edit',  [ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}',       [ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}',    [ProductController::class, 'destroy'])->name('products.destroy');

        // Manajemen pesanan
        Route::get('/orders',            [OrderController::class, 'adminIndex'])->name('orders.index');
        Route::patch('/orders/{order}',  [OrderController::class, 'updateStatus'])->name('orders.update');
        Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    });

// Route bawaan autentikasi (Breeze)
require __DIR__ . '/auth.php';
