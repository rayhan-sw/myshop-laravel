<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString(); // kata kunci search (opsional)

        $products = Product::with(['category', 'images'])
            ->search($q)           // scope di Product
            ->latest()
            ->paginate(5)          // <= batas 5 per halaman
            ->withQueryString();   // <= bawa query q ke pagination links

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create()
    {
        // HANYA SUBKATEGORI (parent_id tidak null) + load parent agar bisa ditampilkan "Parent > Sub"
        $categories = Category::whereNotNull('parent_id')
            ->with('parent:id,name')
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:150'],
            'description' => ['required','string'],
            'price'       => ['required','numeric','min:1', 'max:9999999999999999.99'],
            'stock'       => ['required','integer','min:1'],

            // WAJIB SUBKATEGORI: exists categories.id dengan syarat parent_id NOT NULL
            'category_id' => [
                'required',
                Rule::exists('categories','id')->where(fn($q) => $q->whereNotNull('parent_id')),
            ],

            'images'      => ['required','array','min:1','max:6'],
            'images.*'    => ['required','image','mimes:jpg,jpeg,png,webp','max:2048'],
        ], [
            'category_id.exists' => 'Pilih subkategori (bukan kategori utama).',
        ]);

        DB::transaction(function () use ($data, $request) {
            $paths = [];
            foreach ($request->file('images') as $file) {
                $paths[] = $file->store('products', 'public');
            }

            $primary = $paths[0] ?? null;

            $product = Product::create([
                'name'        => $data['name'],
                'description' => $data['description'],
                'price'       => $data['price'],
                'stock'       => $data['stock'],
                'category_id' => $data['category_id'], // sudah dipastikan subkategori
                'image_path'  => $primary,
            ]);

            foreach ($paths as $i => $path) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image_path' => $path,
                    'sort_order' => $i,
                ]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        // Dropdown kategori: HANYA SUBKATEGORI
        $categories = Category::whereNotNull('parent_id')
            ->with('parent:id,name')
            ->orderBy('name')
            ->get();

        $product->load('images');

        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'        => ['required','string','max:150'],
            'description' => ['required','string'],
            'price'       => ['required','numeric','min:1', 'max:9999999999999999.99'],
            'stock'       => ['required','integer','min:1'],

            // Tetap wajib subkategori saat update
            'category_id' => [
                'required',
                Rule::exists('categories','id')->where(fn($q) => $q->whereNotNull('parent_id')),
            ],

            // APPEND MODE: tambah tanpa menghapus lama
            'images'      => ['nullable','array','min:1','max:6'],
            'images.*'    => ['image','mimes:jpg,jpeg,png,webp','max:2048'],
        ], [
            'category_id.exists' => 'Pilih subkategori (bukan kategori utama).',
        ]);

        DB::transaction(function () use ($data, $request, $product) {
            $product->update([
                'name'        => $data['name'],
                'description' => $data['description'],
                'price'       => $data['price'],
                'stock'       => $data['stock'],
                'category_id' => $data['category_id'], // sudah dipastikan subkategori
            ]);

            if ($request->hasFile('images')) {
                $product->load('images');

                $existingCount = $product->images->count();
                $newCount      = count($request->file('images'));
                $total         = $existingCount + $newCount;

                if ($total > 6) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'images' => ["Total gambar melebihi batas (maks 6). Saat ini {$existingCount}, upload baru {$newCount} → total {$total}."],
                    ]);
                }

                $startOrder = (int) ($product->images()->max('sort_order') ?? -1);

                $paths = [];
                foreach ($request->file('images') as $file) {
                    $paths[] = $file->store('products', 'public');
                }

                foreach ($paths as $i => $path) {
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => $startOrder + $i + 1,
                    ]);
                }

                if (empty($product->image_path)) {
                    $product->update(['image_path' => $paths[0]]);
                }
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated (images appended).');
    }

    public function destroy(Product $product)
    {
        DB::transaction(function () use ($product) {
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }

            $product->load('images');
            foreach ($product->images as $img) {
                if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                    Storage::disk('public')->delete($img->image_path);
                }
                $img->delete();
            }

            $product->delete();
        });

        return back()->with('success', 'Product deleted successfully.');
    }
}
