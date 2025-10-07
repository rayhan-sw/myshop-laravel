@extends('admin.layouts.app')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Edit Product</h1>
  </div>

  <div class="rounded-lg border bg-white p-4">
    <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
      @csrf @method('PUT')

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
        <input id="price" type="number" name="price" required min="1" step="0.01" inputmode="decimal"
               value="{{ old('price', $product->price) }}"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p id="priceRp" class="mt-1 text-xs text-gray-500">
          Rp {{ number_format((float)old('price', $product->price ?? 0), 0, ',', '.') }}
        </p>
        @error('price') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Stock</label>
        <input type="number" min="1" name="stock" value="{{ old('stock', $product->stock) }}" required
               class="mt-1 block w-full rounded-md border px-3 py-2">
        @error('stock') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" required class="mt-1 block w-full rounded-md border px-3 py-2">
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id', $product->category_id) == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Current images (ordered). Primary = index 0. Tampilkan fallback jika tidak ada relasi. --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Current Images</label>
        @php $hasRel = $product->images && $product->images->count() > 0; @endphp
        <div class="flex flex-wrap gap-2">
          @if($hasRel)
            @foreach($product->images as $idx => $img)
              <div class="relative">
                <img src="{{ asset('storage/'.$img->image_path) }}" class="h-20 w-20 rounded object-cover border" alt="">
                <span class="absolute left-1 top-1 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-medium text-white">
                  {{ $idx === 0 ? 'Primary (0)' : '#'.$idx }}
                </span>
              </div>
            @endforeach
          @elseif($product->image_path)
            <div class="relative">
              <img src="{{ asset('storage/'.$product->image_path) }}" class="h-20 w-20 rounded object-cover border" alt="">
              <span class="absolute left-1 top-1 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-medium text-white">Primary</span>
            </div>
          @else
            <div class="text-sm text-gray-500">No images.</div>
          @endif
        </div>
      </div>

      {{-- Upload new images (optional). Jika diisi, semua gambar lama akan diganti.
           Foto pertama → primary (products.image_path) dan sort_order = 0. --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Upload New Images (optional, 1–6)</label>
        <input id="images" type="file" name="images[]" multiple
               accept="image/jpeg,image/png,image/webp"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p class="mt-1 text-xs text-gray-500">Jika mengupload, <b>semua gambar lama akan diganti</b>. Pilih 1–6 foto. JPG/PNG/WEBP, ≤ 2MB/foto.</p>
        @error('images') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        @error('images.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror

        {{-- Preview grid untuk foto baru (urutan = sort_order) --}}
        <div id="previewGrid" class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6"></div>
        <p id="imgCount" class="mt-1 text-xs text-gray-500"></p>
      </div>

      <div class="sm:col-span-2 mt-2">
        <button class="btn-primary rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
          Update Product
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
  // Format Rupiah (0–2 desimal) agar konsisten dengan step=0.01 dan validasi numeric
  const price = document.getElementById('price');
  const priceRp = document.getElementById('priceRp');
  const fmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 2 });
  function updPrice() {
    const v = Number(price.value || 0);
    priceRp.textContent = fmt.format(v);
  }
  if (price && priceRp) {
    price.addEventListener('input', updPrice);
  }

  // Preview & guard jumlah file (maks 6). Urutan preview = sort_order (0..n).
  const inputImages = document.getElementById('images');
  const previewGrid = document.getElementById('previewGrid');
  const imgCount = document.getElementById('imgCount');

  function bytesToMB(b){ return (b / (1024*1024)).toFixed(2); }

  function renderPreviews(files) {
    previewGrid.innerHTML = '';
    [...files].forEach((file, idx) => {
      const url = URL.createObjectURL(file);
      const wrap = document.createElement('div');
      wrap.className = 'relative overflow-hidden rounded border';

      const badge = document.createElement('span');
      badge.textContent = idx === 0 ? 'Primary (0)' : `#${idx}`;
      badge.className = 'absolute left-1 top-1 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-medium text-white';

      const img = document.createElement('img');
      img.src = url;
      img.alt = file.name;
      img.className = 'h-24 w-full object-cover';

      const cap = document.createElement('div');
      cap.className = 'px-1 py-1 text-[10px] text-gray-600 truncate';
      cap.textContent = `${file.name} • ${bytesToMB(file.size)} MB`;

      wrap.appendChild(badge);
      wrap.appendChild(img);
      wrap.appendChild(cap);
      previewGrid.appendChild(wrap);
    });
    if (imgCount) {
      imgCount.textContent = files.length
        ? `Total foto baru: ${files.length} (foto pertama jadi primary / sort_order = 0)`
        : '';
    }
  }

  if (inputImages) {
    inputImages.addEventListener('change', (e) => {
      const files = e.target.files;

      // Batasi maksimal 6 file (server tetap memvalidasi)
      if (files.length > 6) {
        alert('Maksimal 6 foto.');
        const dt = new DataTransfer();
        [...files].slice(0, 6).forEach(f => dt.items.add(f));
        inputImages.files = dt.files;
      }

      renderPreviews(inputImages.files);
    });
  }
</script>
@endpush
