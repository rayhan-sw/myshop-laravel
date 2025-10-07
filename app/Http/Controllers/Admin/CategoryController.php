<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // LIST ROOT + tombol Manage Subcategories
    public function index()
    {
        $roots = Category::whereNull('parent_id')
            ->withCount('children')
            ->orderBy('name')
            ->paginate(10);

        return view('admin.categories.index', compact('roots'));
    }

    // HALAMAN SUB UNTUK 1 PARENT
    public function show(Category $category) // $category = parent (root)
    {
        abort_if(!is_null($category->parent_id), 404); // pastikan ini root

        $subs = $category->children()->orderBy('name')->paginate(10);

        return view('admin.categories.sub', [
            'parent' => $category,
            'subs'   => $subs,
        ]);
    }

    // CREATE (root atau sub)
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:100'],
            'parent_id' => ['nullable','integer','exists:categories,id'],
        ]);

        // Batasi level: kalau ada parent -> parent harus ROOT
        if (!empty($data['parent_id'])) {
            $isRoot = Category::where('id', $data['parent_id'])->whereNull('parent_id')->exists();
            if (!$isRoot) return back()->with('error','Parent harus kategori utama.')->withInput();
        }

        // Unique per parent
        $exists = Category::where('name',$data['name'])
            ->where('parent_id', $data['parent_id'] ?? null)->exists();
        if ($exists) return back()->with('error','Nama kategori sudah ada pada parent tersebut.')->withInput();

        $cat = Category::create($data);

        // redirect: jika sub dibuat dari halaman parent, kembali ke halaman parent
        if (!empty($data['parent_id'])) {
            return redirect()->route('admin.categories.show', $data['parent_id'])->with('success','Subcategory created.');
        }
        return redirect()->route('admin.categories.index')->with('success','Root category created.');
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name'      => ['required','string','max:100'],
            'parent_id' => ['nullable','integer','exists:categories,id'],
        ]);

        // Tidak boleh parent diri sendiri
        if (!empty($data['parent_id']) && (int)$data['parent_id'] === (int)$category->id) {
            return back()->with('error','Kategori tidak boleh menjadi parent dirinya sendiri.')->withInput();
        }

        // Batasi level: parent harus root (atau null utk root)
        if (!empty($data['parent_id'])) {
            $isRoot = Category::where('id',$data['parent_id'])->whereNull('parent_id')->exists();
            if (!$isRoot) return back()->with('error','Parent harus kategori utama.')->withInput();
        }

        // Unique per parent (abaikan diri sendiri)
        $exists = Category::where('name',$data['name'])
            ->where('parent_id',$data['parent_id'] ?? null)
            ->where('id','!=',$category->id)
            ->exists();
        if ($exists) return back()->with('error','Nama kategori sudah ada pada parent tersebut.')->withInput();

        $category->update($data);

        // redirect ke tempat yang pas
        if (!is_null($category->parent_id)) {
            return redirect()->route('admin.categories.show', $category->parent_id)->with('success','Subcategory updated.');
        }
        return redirect()->route('admin.categories.index')->with('success','Root category updated.');
    }

    public function destroy(Category $category)
    {
        // Larang hapus ROOT yang masih punya sub
        if (is_null($category->parent_id) && $category->children()->exists()) {
            return back()->with('error','Hapus subkategori terlebih dahulu sebelum menghapus kategori utama.');
        }

        $parentId = $category->parent_id;
        $category->delete();

        // redirect kembali ke halaman asal
        if (!is_null($parentId)) {
            return redirect()->route('admin.categories.show', $parentId)->with('success','Subcategory deleted.');
        }
        return redirect()->route('admin.categories.index')->with('success','Root category deleted.');
    }
}
