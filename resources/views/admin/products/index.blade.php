@extends('admin.layouts.app')

@section('content')
  <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h1 class="text-2xl font-semibold">Products</h1>

    <div class="flex flex-col sm:flex-row sm:items-center gap-2 w-full sm:w-auto">
      {{-- Form pencarian produk --}}
      <form action="{{ route('admin.products.index') }}" method="GET" class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
        <input
          type="text"
          name="q"
          value="{{ $q ?? request('q') }}"
          placeholder="Search name, desc, category..."
          class="rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2 text-sm w-full sm:w-64 shadow-sm"
        />
        <div class="flex gap-2">
          <button
            type="submit"
            class="rounded-md bg-indigo-600 text-white px-4 py-2 text-sm font-medium hover:bg-indigo-700 transition w-full sm:w-auto">
            Search
          </button>
          @if(($q ?? request('q')))
            <a href="{{ route('admin.products.index') }}"
              class="rounded-md bg-gray-100 text-gray-700 px-4 py-2 text-sm font-medium hover:bg-gray-200 transition w-full sm:w-auto text-center">
              Reset
            </a>
          @endif
        </div>
      </form>

      {{-- Tombol tambah produk --}}
      <a href="{{ route('admin.products.create') }}"
        class="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700 transition w-full sm:w-auto text-center">
        + Add Product
      </a>
    </div>
  </div>

  {{-- Informasi hasil pencarian --}}
  @if(($q ?? request('q')))
    <div class="mb-3 text-sm text-gray-600">
      Showing results for: <span class="font-medium">"{{ $q ?? request('q') }}"</span>
      — page {{ $products->currentPage() }} of {{ $products->lastPage() }},
      total {{ $products->total() }} items
    </div>
  @endif

  <div class="overflow-x-auto rounded-lg border bg-white">
    <table class="min-w-full w-full text-left text-sm">
      <thead class="border-b bg-gray-50 text-gray-600">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">Image</th>
          <th class="px-3 py-2">Name</th>
          <th class="px-3 py-2">Category</th>
          <th class="px-3 py-2">Price</th>
          <th class="px-3 py-2">Stock</th>
          <th class="px-3 py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($products as $p)
          <tr class="border-b last:border-0 align-middle hover:bg-gray-50 transition">
            <td class="px-2 py-1 sm:px-3 sm:py-2">
              {{ $loop->iteration + (($products->currentPage() - 1) * $products->perPage()) }}
            </td>

            {{-- Thumbnail produk (prioritas: primaryImageUrl -> first image -> placeholder) --}}
            <td class="px-2 py-1 sm:px-3 sm:py-2">
              @php
                $thumb = null;
                try {
                  if (method_exists($p, 'primaryImageUrl')) {
                    $thumb = $p->primaryImageUrl();
                  }
                } catch (\Throwable $e) {}
                if (!$thumb) {
                  $thumb = optional($p->images->first())->url; // accessor 'url' pada ProductImage
                }
              @endphp

              @if($thumb)
                <div class="relative">
                  <img src="{{ $thumb }}" alt="thumb" class="h-12 w-12 rounded object-cover border">
                  @if($p->images && $p->images->count() > 1)
                    <span class="absolute -right-1 -top-1 rounded bg-black/70 px-1.5 py-0.5 text-[10px] font-medium text-white">
                      {{ $p->images->count() }}
                    </span>
                  @endif
                </div>
              @else
                <div class="h-12 w-12 rounded bg-gray-100 border"></div>
              @endif
            </td>

            <td class="px-2 py-1 sm:px-3 sm:py-2">{{ $p->name }}</td>
            <td class="px-2 py-1 sm:px-3 sm:py-2">{{ $p->category?->name ?? '—' }}</td>
            <td class="px-2 py-1 sm:px-3 sm:py-2">{{ $p->price_formatted }}</td>
            <td class="px-2 py-1 sm:px-3 sm:py-2">{{ $p->stock }}</td>

            {{-- Aksi edit / hapus --}}
            <td class="px-3 py-2 text-right space-x-1">
              <a href="{{ route('admin.products.edit', $p) }}"
                 class="rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50 font-medium transition">
                Edit
              </a>

              {{-- Hapus dengan SweetAlert (gunakan .js-delete) --}}
              <form action="{{ route('admin.products.destroy', $p) }}" method="POST" class="inline js-delete">
                @csrf
                @method('DELETE')
                <button class="rounded px-3 py-1 text-rose-600 hover:bg-rose-50 font-medium transition">
                  Delete
                </button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="7" class="px-3 py-6 text-center text-gray-500">
              {{ ($q ?? request('q')) ? 'No products match your search.' : 'No products.' }}
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Paginasi (styled) --}}
  <div class="mt-6 flex justify-center">
    @if ($products->hasPages())
      <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-1">
        {{-- Tombol sebelumnya --}}
        @if ($products->onFirstPage())
          <span class="px-3 py-1.5 rounded bg-gray-200 text-gray-500 cursor-default">&lt;</span>
        @else
          <a href="{{ $products->previousPageUrl() }}" class="px-3 py-1.5 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">&lt;</a>
        @endif

        {{-- Nomor halaman --}}
        @foreach ($products->getUrlRange(1, $products->lastPage()) as $page => $url)
          @if ($page == $products->currentPage())
            <span class="px-3 py-1.5 rounded bg-indigo-600 text-white font-medium">{{ $page }}</span>
          @else
            <a href="{{ $url }}" class="px-3 py-1.5 rounded bg-gray-100 text-gray-700 hover:bg-gray-200 transition">{{ $page }}</a>
          @endif
        @endforeach

        {{-- Tombol berikutnya --}}
        @if ($products->hasMorePages())
          <a href="{{ $products->nextPageUrl() }}" class="px-3 py-1.5 rounded bg-indigo-600 text-white hover:bg-indigo-700 transition">&gt;</a>
        @else
          <span class="px-3 py-1.5 rounded bg-gray-200 text-gray-500 cursor-default">&gt;</span>
        @endif
      </nav>
    @endif
  </div>
@endsection
