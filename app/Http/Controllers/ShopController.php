<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class ShopController extends Controller
{
    // HOME (opsional) — kamu sekarang pakai closure di web.php
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

    // SHOP: listing + filter q, root_id, sub_id
    public function shop(Request $r)
    {
        $q       = trim((string)$r->query('q', ''));
        $rootId  = $r->integer('root_id') ?: null; // kategori utama
        $subId   = $r->integer('sub_id')  ?: null; // subkategori

        $products = Product::with(['category.parent','images'])
            ->when(Schema::hasColumn('products','is_active'), fn($qq) => $qq->where('is_active', true))
            ->when($q,      fn($qq) => $qq->where('name', 'like', "%{$q}%"))
            ->when($rootId, fn($qq) => $qq->whereHas('category', fn($c) => $c->where('parent_id', $rootId)))
            ->when($subId,  fn($qq) => $qq->where('category_id', $subId))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        // Root + anak-anaknya untuk filter
        $roots = Category::with(['children' => fn($q) => $q->orderBy('name')])
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

    // DETAIL PRODUCT
    public function show(Product $product)
    {
        // muat relasi yang dibutuhkan
        $product->load(['category.parent', 'images']);

        // produk terkait (kategori sama, bukan dirinya)
        $related = Product::with(['category.parent','images'])
            ->when(Schema::hasColumn('products','is_active'), fn($q) => $q->where('is_active', true))
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->latest()
            ->take(8)
            ->get();

        return Inertia::render('ProductDetail', [
            'product' => $product,
            'related' => $related,
        ]);
    }
}
