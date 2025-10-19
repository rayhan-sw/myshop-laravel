<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ShopController extends Controller
{
    // Landing: kategori root + produk terbaru
    public function home()
    {
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
    }

    // Listing katalog
    public function shop(Request $r)
    {
        $q       = trim((string)$r->query('q', '')); // pencarian nama
        $rootId  = $r->integer('root_id') ?: null;  // filter kategori root
        $subId   = $r->integer('sub_id')  ?: null; // filter subkategori

        $products = Product::with([
                'category.parent',
                'images' => fn($iq) => $iq->orderByDesc('is_primary')->orderBy('id'),
            ])
            ->when(Schema::hasColumn('products','is_active'), fn($qq) => $qq->where('is_active', true)) // hanya aktif bila kolom ada
            ->when($q,      fn($qq) => $qq->where('name', 'like', "%{$q}%"))  // filter nama
            ->when($rootId, fn($qq) => $qq->whereHas('category', fn($c) => $c->where('parent_id', $rootId))) // filter root
            ->when($subId,  fn($qq) => $qq->where('category_id', $subId))  // filter sub
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $roots = Category::with(['children' => fn($cq) => $cq->orderBy('name')])
            ->whereNull('parent_id')
            ->orderBy('name')
            ->get(['id','name','parent_id']);

        return Inertia::render('Shop', [
            'products' => $products,
            'roots'    => $roots,
            'filters'  => [
                'q'       => $q,
                'root_id' => $rootId,
                'sub_id'  => $subId,
            ],
        ]);
    }

    // Detail produk 
    public function show(Product $product)
    {
        // Lengkapi relasi untuk tampilan detail
        $product->load([
            'images' => fn($iq) => $iq->orderByDesc('is_primary')->orderBy('id'),
            'category.parent',
        ]);

        // Produk terkait satu kategori
        $related = Product::with([
                'images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
                'category.parent',
            ])
            ->when(Schema::hasColumn('products','is_active'), fn($q) => $q->where('is_active', true))
            ->where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->inRandomOrder()
            ->take(8)
            ->get();

        // Render ke komponen ProductDetail.vue
        return Inertia::render('ProductDetail', [
            'product' => $product,  // frontend mengakses product.stock
            'related' => $related,
        ]);
    }
}
