<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Daftar kategori root (beserta jumlah sub) + tombol kelola subkategori
    public function index()
    {
        $roots = Category::whereNull('parent_id')
            ->withCount('children')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.categories.index', compact('roots'));
    }

    // Halaman subkategori untuk satu parent (wajib root)
    public function show(Category $category) // $category = parent (root)
    {
        abort_if(!is_null($category->parent_id), 404); // Validasi: hanya root yang boleh ditampilkan

        $subs = $category->children()->orderBy('name')->paginate(10);

        return view('admin.categories.sub', [
            'parent' => $category,
            'subs'   => $subs,
        ]);
    }

    // Buat kategori baru (root atau sub)
    public function store(Request $request)
    {
        // Validasi dasar
        $data = $request->validate([
            'name'      => ['required','string','max:100'],
            'parent_id' => ['nullable','integer','exists:categories,id'],
        ]);

        // Batasi kedalaman: jika ada parent, parent harus root
        if (!empty($data['parent_id'])) {
            $isRoot = Category::where('id', $data['parent_id'])->whereNull('parent_id')->exists();
            if (!$isRoot) return back()->with('error','Parent harus kategori utama.')->withInput();
        }

        // Unik per parent
        $exists = Category::where('name',$data['name'])
            ->where('parent_id', $data['parent_id'] ?? null)
            ->exists();
        if ($exists) return back()->with('error','Nama kategori sudah ada pada parent tersebut.')->withInput();

        $cat = Category::create($data);

        // Arahkan sesuai konteks pembuatan (root/sub)
        if (!empty($data['parent_id'])) {
            return redirect()->route('admin.categories.show', $data['parent_id'])->with('success','Subcategory created.');
        }
        return redirect()->route('admin.categories.index')->with('success','Root category created.');
    }

    // Ubah kategori (root atau sub)
    public function update(Request $request, Category $category)
    {
        // Validasi dasar
        $data = $request->validate([
            'name'      => ['required','string','max:100'],
            'parent_id' => ['nullable','integer','exists:categories,id'],
        ]);

        // Cegah parent diri sendiri
        if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$category->id) {
            return back()->with('error','Kategori tidak boleh menjadi parent dirinya sendiri.')->withInput();
        }

        // Batasi kedalaman: parent (jika ada) wajib root
        if (!empty($data['parent_id'])) {
            $isRoot = Category::where('id',$data['parent_id'])->whereNull('parent_id')->exists();
            if (!$isRoot) return back()->with('error','Parent harus kategori utama.')->withInput();
        }

        // Unik per parent (abaikan diri sendiri)
        $exists = Category::where('name',$data['name'])
            ->where('parent_id',$data['parent_id'] ?? null)
            ->where('id','!=',$category->id)
            ->exists();
        if ($exists) return back()->with('error','Nama kategori sudah ada pada parent tersebut.')->withInput();

        $category->update($data);

        // Arahkan sesuai level kategori
        if (!is_null($category->parent_id)) {
            return redirect()->route('admin.categories.show', $category->parent_id)->with('success','Subcategory updated.');
        }
        return redirect()->route('admin.categories.index')->with('success','Root category updated.');
    }

    // Hapus kategori (blokir root yang masih memiliki sub)
    public function destroy(Category $category)
    {
        // Cegah hapus root yang masih punya sub
        if (is_null($category->parent_id) && $category->children()->exists()) {
            return back()->with('error','Hapus subkategori terlebih dahulu sebelum menghapus kategori utama.');
        }

        $parentId = $category->parent_id;
        $category->delete();

        // Arahkan kembali ke halaman asal (parent/root)
        if (!is_null($parentId)) {
            return redirect()->route('admin.categories.show', $parentId)->with('success','Subcategory deleted.');
        }
        return redirect()->route('admin.categories.index')->with('success','Root category deleted.');
    }
}
