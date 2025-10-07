@extends('admin.layouts.app')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Add Product</h1>
  </div>

  <div class="rounded-lg border bg-white p-4">
    <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">
      @csrf

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Product Name</label>
        <input type="text" name="name" value="{{ old('name') }}" required maxlength="150"
               class="mt-1 block w-full rounded-md border px-3 py-2" autocomplete="off">
        @error('name') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Description</label>
        <textarea name="description" rows="4" required
                  class="mt-1 block w-full rounded-md border px-3 py-2">{{ old('description') }}</textarea>
        @error('description') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Price</label>
        <input id="price" type="number" name="price" required min="1" step="0.01" inputmode="decimal"
               value="{{ old('price') }}"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p id="priceRp" class="mt-1 text-xs text-gray-500">Rp 0</p>
        @error('price') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Stock</label>
        <input type="number" min="1" name="stock" value="{{ old('stock', 1) }}" required
               class="mt-1 block w-full rounded-md border px-3 py-2">
        @error('stock') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700">Category</label>
        <select name="category_id" required class="mt-1 block w-full rounded-md border px-3 py-2">
          <option value="" disabled {{ old('category_id') ? '' : 'selected' }}>-- Select Category --</option>
          @foreach($categories as $c)
            <option value="{{ $c->id }}" @selected(old('category_id') == $c->id)>{{ $c->name }}</option>
          @endforeach
        </select>
        @error('category_id') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
      </div>

      {{-- Multi Images (1–6), foto pertama = primary (image_path), urutan file = sort_order --}}
      <div class="sm:col-span-2">
        <label class="block text-sm font-medium text-gray-700">Product Images (1–6)</label>
        <input id="images" type="file" name="images[]" required multiple
               accept="image/jpeg,image/png,image/webp"
               class="mt-1 block w-full rounded-md border px-3 py-2">
        <p class="mt-1 text-xs text-gray-500">
          Pilih minimal 1, maksimal 6 foto. Format: JPG/PNG/WEBP, ukuran ≤ 2MB/foto.
        </p>
        @error('images') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror
        @error('images.*') <p class="mt-1 text-sm text-rose-600">{{ $message }}</p> @enderror>

        {{-- Preview grid (opsional, memudahkan melihat urutan yang akan menjadi sort_order) --}}
        <div id="previewGrid" class="mt-3 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6"></div>
        <p id="imgCount" class="mt-1 text-xs text-gray-500"></p>
      </div>

      <div class="sm:col-span-2 mt-2">
        <button class="btn-primary rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
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
  // Format Rupiah (maks 2 desimal agar konsisten dengan step=0.01)
  const price = document.getElementById('price');
  const priceRp = document.getElementById('priceRp');
  const fmt = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0, maximumFractionDigits: 2 });
  function updPrice() {
    const v = Number(price.value || 0);
    priceRp.textContent = fmt.format(v);
  }
  if (price && priceRp) {
    price.addEventListener('input', updPrice);
    updPrice();
  }

  // Preview & guard jumlah file (maks 6) — urutan di sini mengikuti sort_order (0..n)
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
        ? `Total foto: ${files.length} (foto pertama jadi primary / sort_order = 0)`
        : '';
    }
  }

  if (inputImages) {
    inputImages.addEventListener('change', (e) => {
      const files = e.target.files;

      // Batasi maksimal 6 file di sisi klien (server tetap memvalidasi)
      if (files.length > 6) {
        alert('Maksimal 6 foto.');
        // Potong ke 6 (opsional: minta user pilih ulang)
        const dt = new DataTransfer();
        [...files].slice(0, 6).forEach(f => dt.items.add(f));
        inputImages.files = dt.files;
      }

      renderPreviews(inputImages.files);
    });
  }
</script>
@endpush
