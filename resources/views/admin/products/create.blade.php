@extends('admin.layouts.app')
@section('title','Create Product')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Add Product</h1>
  </div>

  <div class="rounded-lg border bg-white p-4">
    {{-- Form pembuatan produk --}}
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
      @csrf

      {{-- Nama produk --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Product Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="150"
               class="mt-1 block w-full rounded-md border px-3 py-2" autocomplete="off">
        @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Deskripsi --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4" required
                  class="mt-1 block w-full rounded-md border px-3 py-2">{{ old('description') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Harga + tampilan rupiah dinamis --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input id="price" type="number" name="price" required min="0" step="0.01" inputmode="decimal"
               value="{{ old('price') }}"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p id="priceRp" class="mt-1 text-xs text-gray-500">Rp 0</p>
        @error('price') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Stok --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Stock</label>
        <input type="number" min="0" name="stock" value="{{ old('stock', 0) }}" required
               class="mt-1 block w-full rounded-md border px-3 py-2">
        @error('stock') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Kategori (root/sub) --}}
      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" required class="mt-1 block w-full rounded-md border px-3 py-2">
          <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Select Category --</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>
              {{ $c->parent?->name ? ($c->parent->name.' → '.$c->name) : $c->name }}
            </option>
          @endforeach
        </select>
      </div>

      {{-- Upload gambar & pilih gambar utama (Primary) --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Images</label>
        <input type="file" name="images[]" accept="image/*" multiple
               onchange="previewNewImages(this)"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p class="mt-1 text-xs text-gray-500">
          Pilih beberapa foto (maks 6). Centang salah satu sebagai <b>Primary</b>.
        </p>
        @error('images') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        @error('images.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

        <input type="hidden" name="primary_index" id="primary_index" value="0"> {{-- Indeks gambar utama --}}
        <div id="newImagePreview" class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6"></div>
      </div>

      {{-- Aksi --}}
      <div class="sm:col-span-2 mt-2">
        <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
          Save Product
        </button>
        <a href="{{ route('admin.products.index') }}" class="ml-2 rounded px-4 py-2 text-sm text-gray-600 hover:bg-gray-100">
          Cancel
        </a>
      </div>
    </form>
  </div>
@endsection

@push('scripts')
<script>
  // Tampilkan harga dalam format Rupiah (live)
  (function(){
    const price = document.getElementById('price');
    const priceRp = document.getElementById('priceRp');
    if (!price || !priceRp) return;
    const fmt = new Intl.NumberFormat('id-ID', { style:'currency', currency:'IDR', minimumFractionDigits:0 });
    function upd(){ priceRp.textContent = fmt.format(Number(price.value || 0)); }
    price.addEventListener('input', upd); upd();
  })();

  // Pratinjau file gambar dan pilih Primary
  function previewNewImages(input) {
    const container = document.getElementById('newImagePreview');
    const primaryEl = document.getElementById('primary_index');
    container.innerHTML = '';

    let files = Array.from(input.files || []);
    if (files.length > 6) { files = files.slice(0,6); } // Batasi 6 gambar

    files.forEach((file, i) => {
      const url = URL.createObjectURL(file);
      const card = document.createElement('div');
      card.className = 'rounded border p-2 text-center';

      card.innerHTML = `
        <img src="${url}" class="mb-2 h-24 w-full rounded object-cover">
        <label class="inline-flex items-center gap-1 text-sm">
          <input type="radio" name="primary_radio" class="h-4 w-4" ${i===0?'checked':''}>
          <span>Primary</span>
        </label>
      `;
      card.querySelector('input[type="radio"]').addEventListener('change', () => primaryEl.value = i);
      container.appendChild(card);
    });

    if (!files.length) primaryEl.value = 0; // Reset jika tidak ada file
  }
</script>
@endpush
