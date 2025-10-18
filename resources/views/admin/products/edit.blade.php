@extends('admin.layouts.app')
@section('title','Edit Product')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Edit Product</h1>
  </div>

  <div class="rounded-lg border bg-white p-4">
    {{-- Form pengubahan data produk --}}
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
      @csrf @method('PUT')

      {{-- Field dasar --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Product Name</label>
        <input type="text" name="name" value="{{ old('name', $product->name) }}" required maxlength="150"
               class="mt-1 block w-full rounded-md border px-3 py-2" autocomplete="off">
        @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4" required
                  class="mt-1 block w-full rounded-md border px-3 py-2">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input id="price" type="number" name="price" required min="0" step="0.01" inputmode="decimal"
               value="{{ old('price', $product->price) }}"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p id="priceRp" class="mt-1 text-xs text-gray-500">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
        @error('price') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Stock</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', $product->stock) }}" required
               class="mt-1 block w-full rounded-md border px-3 py-2">
        @error('stock') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" required class="mt-1 block w-full rounded-md border px-3 py-2">
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>
              {{ $c->parent?->name ? ($c->parent->name.' → '.$c->name) : $c->name }}
            </option>
          @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Gambar yang sudah ada (pilih Primary atau tandai hapus) --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Existing Images</label>
        <div class="mt-2 flex flex-wrap gap-3">
          @forelse($product->images as $img)
            <div class="w-36 border rounded p-2 text-center">
              <img src="{{ $img->url }}" alt="" class="h-24 w-full object-cover rounded mb-2">
              <div>
                <label class="inline-flex items-center gap-1 text-sm">
                  <input type="radio" name="primary_image_id" value="{{ $img->id }}" class="h-4 w-4"
                         @checked($img->is_primary)>
                  <span>Primary</span>
                </label>
              </div>
              <div class="mt-1">
                <label class="inline-flex items-center gap-1 text-sm text-red-600">
                  <input type="checkbox" name="delete_images[]" value="{{ $img->id }}" class="h-4 w-4">
                  <span>Delete</span>
                </label>
              </div>
            </div>
          @empty
            <p class="text-sm text-gray-500">No images.</p>
          @endforelse
        </div>
      </div>

      {{-- Tambah gambar baru (dengan pratinjau dan pilih Primary) --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Add New Images</label>
        <input type="file" name="new_images[]" accept="image/*" multiple
               onchange="previewAddImages(this)"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p class="mt-1 text-xs text-gray-500">
          Tambahkan beberapa foto baru (maks 6). Pilih salah satu sebagai <b>Primary</b>.
        </p>
        <div id="addImagePreview" class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6"></div>
      </div>

      {{-- Aksi --}}
      <div class="sm:col-span-2 mt-2">
        <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
          Update Product
        </button>
        <a href="{{ route('admin.products.index') }}"
           class="ml-2 rounded px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">
          Cancel
        </a>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  // Tampilkan harga dalam format Rupiah saat input berubah
  const price = document.getElementById('price');
  const priceRp = document.getElementById('priceRp');
  const fmt = new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 });
  if (price && priceRp) price.addEventListener('input', () => priceRp.textContent = fmt.format(Number(price.value || 0)));

  // Pratinjau gambar baru dan opsi pilih Primary (untuk upload baru)
  function previewAddImages(input){
    const container = document.getElementById('addImagePreview');
    container.innerHTML = '';
    [...(input.files||[])].forEach((file,i)=>{
      const url = URL.createObjectURL(file);
      const key = `new_${i}`; // penanda unik gambar baru (client-side)
      const el = document.createElement('div');
      el.className = 'border rounded p-2 text-center';
      el.innerHTML = `
        <img src="${url}" class="h-24 w-full object-cover mb-2 rounded">
        <label class="inline-flex items-center gap-1 text-sm">
          <input type="radio" name="primary_image_id" value="${key}" class="h-4 w-4">
          <span>Primary</span>
        </label>`;
      container.appendChild(el);
    });
  }
</script>
@endpush
