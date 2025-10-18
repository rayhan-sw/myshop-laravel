@extends('admin.layouts.app')

@section('content')
  {{-- Header halaman --}}
  <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h1 class="text-2xl font-semibold">Root Categories</h1>
  </div>

  {{-- Pesan notifikasi (Flash Messages) --}}
  @if(session('success'))
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
      {{ session('success') }}
    </div>
  @endif
  @if(session('error'))
    <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
      {{ session('error') }}
    </div>
  @endif

  {{-- Form tambah kategori utama --}}
  <div class="mb-6 rounded-lg border bg-white p-4">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="flex flex-col sm:flex-row gap-3 sm:items-center">
      @csrf
      <input type="text" name="name" value="{{ old('name') }}"
             class="flex-1 rounded-md border px-3 py-2"
             placeholder="Nama kategori utama (contoh: Pria, Wanita)" required maxlength="100">
      <input type="hidden" name="parent_id" value="">
      <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 transition">Add Root</button>
    </form>
    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  {{-- Tabel daftar kategori utama --}}
  <div class="overflow-x-auto rounded-lg border bg-white">
    <table class="min-w-full w-full text-left text-sm">
      <thead class="border-b bg-gray-50 text-gray-600">
        <tr>
          <th class="px-2 py-2 sm:px-3 sm:py-2">#</th>
          <th class="px-2 py-2 sm:px-3 sm:py-2">Name</th>
          <th class="px-2 py-2 sm:px-3 sm:py-2">Subcategories</th>
          <th class="px-2 py-2 sm:px-3 sm:py-2">Created at</th>
          <th class="px-2 py-2 sm:px-3 sm:py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($roots as $root)
          <tr class="border-b last:border-0">
            <td class="px-2 py-2 sm:px-3 sm:py-2">
              {{ $loop->iteration + (($roots->currentPage()-1) * $roots->perPage()) }}
            </td>
            <td class="px-2 py-2 sm:px-3 sm:py-2 font-medium">{{ $root->name }}</td>
            <td class="px-2 py-2 sm:px-3 sm:py-2">{{ $root->children_count }}</td>
            <td class="px-2 py-2 sm:px-3 sm:py-2">{{ $root->created_at?->format('Y-m-d H:i') }}</td>
            <td class="px-2 py-2 sm:px-3 sm:py-2 text-right">
              <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end space-y-1 sm:space-y-0 sm:space-x-1">
                <a href="{{ route('admin.categories.show',$root) }}" class="rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50 text-center">Manage Subcategories</a>

                {{-- Form edit kategori utama --}}
                <details class="inline-block text-left">
                  <summary class="inline cursor-pointer rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50 text-center">Edit</summary>
                  <form method="POST" action="{{ route('admin.categories.update', $root) }}" class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2">
                    @csrf @method('PUT')
                    <input type="text" name="name" value="{{ old('name',$root->name) }}" class="flex-1 rounded-md border px-3 py-1" required maxlength="100">
                    <input type="hidden" name="parent_id" value="">
                    <button class="rounded bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">Save</button>
                  </form>
                </details>

                {{-- Tombol hapus kategori utama --}}
                <form action="{{ route('admin.categories.destroy', $root) }}" method="POST" class="inline js-delete">
                  @csrf @method('DELETE')
                  <button class="rounded px-3 py-1 text-rose-600 hover:bg-rose-50 text-center">Delete</button>
                </form>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="5" class="px-2 py-6 sm:px-3 sm:py-6 text-center text-gray-500">
              Belum ada kategori utama.
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4">{{ $roots->links() }}</div>
@endsection
