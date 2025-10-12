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
        $q = $request->string('q')->toString();

        $products = Product::with([
                'category.parent',
                'images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('id'),
            ])
            ->search($q)
            ->latest()
            ->paginate(5)
            ->withQueryString();

        return view('admin.products.index', compact('products', 'q'));
    }

    public function create()
    {
        $categories = Category::whereNotNull('parent_id')
            ->with('parent:id,name')
            ->orderBy('name')
            ->get();

        return view('admin.products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'          => 'required|string|max:255',
            'category_id'   => 'required|exists:categories,id',
            'price'         => 'required|numeric|min:0',
            'stock'         => 'required|integer|min:0',
            'description'   => 'nullable|string',
            'images.*'      => 'nullable|image|max:4096',
            'primary_index' => 'nullable|integer|min:0',
        ]);

        DB::transaction(function () use ($data, $request) {
            // Simpan produk (hindari memasukkan key yang bukan kolom products)
            $product = Product::create([
                'name'        => $data['name'],
                'category_id' => $data['category_id'],
                'price'       => $data['price'],
                'stock'       => $data['stock'],
                'description' => $data['description'] ?? null,
            ]);

            // Upload gambar baru → simpan ke product_images.image_path
            $uploaded = [];
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $i => $file) {
                    $path = $file->store('products', 'public');
                    $uploaded[] = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,   // ← gunakan image_path
                        'is_primary' => false,
                    ]);
                }
            }

            // Set primary berdasarkan primary_index
            if (count($uploaded)) {
                $primaryIndex = (int) ($data['primary_index'] ?? 0);
                $primaryIndex = max(0, min($primaryIndex, count($uploaded) - 1));
                $uploaded[$primaryIndex]->update(['is_primary' => true]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function edit(Product $product)
    {
        $categories = Category::whereNotNull('parent_id')
            ->with('parent:id,name')
            ->orderBy('name')
            ->get();

        $product->load(['images' => fn($q) => $q->orderByDesc('is_primary')->orderBy('id')]);

        return view('admin.products.edit', compact('product','categories'));
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate([
            'name'             => 'required|string|max:255',
            'category_id'      => 'required|exists:categories,id',
            'price'            => 'required|numeric|min:0',
            'stock'            => 'required|integer|min:0',
            'description'      => 'nullable|string',

            // kelola gambar lama
            'delete_images'    => 'array',
            'delete_images.*'  => 'integer|exists:product_images,id',
            'primary_image_id' => 'nullable|string', // id lama atau "new_0"

            // unggah gambar baru
            'new_images.*'     => 'nullable|image|max:4096',
        ]);

        DB::transaction(function () use ($data, $request, $product) {
            // Update field produk
            $product->update([
                'name'        => $data['name'],
                'category_id' => $data['category_id'],
                'price'       => $data['price'],
                'stock'       => $data['stock'],
                'description' => $data['description'] ?? null,
            ]);

            // 1) Hapus gambar lama yang dicentang
            if (!empty($data['delete_images'])) {
                $toDel = $product->images()->whereIn('id', $data['delete_images'])->get();
                foreach ($toDel as $img) {
                    if ($img->image_path && Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);  // ← pakai image_path
                    }
                    $img->delete();
                }
            }

            // 2) Upload gambar baru → simpan ke image_path
            $newMap = []; // key: "new_0" => ProductImage
            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $i => $file) {
                    $path = $file->store('products', 'public');
                    $key  = "new_{$i}";
                    $newMap[$key] = ProductImage::create([
                        'product_id' => $product->id,
                        'image_path' => $path,   // ← pakai image_path
                        'is_primary' => false,
                    ]);
                }
            }

            // 3) Reset semua primary
            $product->images()->update(['is_primary' => false]);

            // 4) Tentukan primary dari pilihan (id lama / new_x)
            $primaryKey = $data['primary_image_id'] ?? null;
            $primaryImg = null;

            if ($primaryKey) {
                if (is_numeric($primaryKey)) {
                    $primaryImg = $product->images()->where('id', $primaryKey)->first();
                } elseif (isset($newMap[$primaryKey])) {
                    $primaryImg = $newMap[$primaryKey];
                }
            }

            // fallback jika tidak ada yang dipilih
            if (!$primaryImg) {
                $primaryImg = $product->images()->first();
            }

            if ($primaryImg) {
                $primaryImg->update(['is_primary' => true]);
            }
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product)
    {
        // Eloquent delete akan memicu hook pada Product & ProductImage
        $product->load('images');
        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }
}
