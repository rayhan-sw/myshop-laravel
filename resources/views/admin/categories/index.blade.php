@extends('admin.layouts.app')

@section('content')
  <div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Root Categories</h1>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">{{ session('error') }}</div>
  @endif

  {{-- Form tambah ROOT --}}
  <div class="mb-6 rounded-lg border bg-white p-4">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="flex flex-col gap-3 sm:flex-row">
      @csrf
      <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border px-3 py-2" placeholder="Nama kategori utama (contoh: Pria, Wanita)" required maxlength="100">
      <input type="hidden" name="parent_id" value="">
      <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Add Root</button>
    </form>
    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  {{-- Tabel ROOT --}}
  <div class="overflow-x-auto rounded-lg border bg-white">
    <table class="min-w-[720px] w-full text-left text-sm">
      <thead class="border-b bg-gray-50 text-gray-600">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">Name</th>
          <th class="px-3 py-2">Subcategories</th>
          <th class="px-3 py-2">Created at</th>
          <th class="px-3 py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($roots as $root)
          <tr class="border-b last:border-0">
            <td class="px-3 py-2">{{ $loop->iteration + (($roots->currentPage()-1) * $roots->perPage()) }}</td>
            <td class="px-3 py-2 font-medium">{{ $root->name }}</td>
            <td class="px-3 py-2">{{ $root->children_count }}</td>
            <td class="px-3 py-2">{{ $root->created_at?->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2 text-right">
              <a href="{{ route('admin.categories.show',$root) }}" class="rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50">Manage Subcategories</a>

              {{-- Edit ROOT --}}
              <details class="inline-block text-left align-middle">
                <summary class="inline cursor-pointer rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50">Edit</summary>
                <form method="POST" action="{{ route('admin.categories.update', $root) }}" class="mt-2 flex items-center gap-2 js-edit">
                  @csrf @method('PUT')
                  <input type="text" name="name" value="{{ old('name',$root->name) }}" class="w-56 rounded-md border px-3 py-1" required maxlength="100">
                  <input type="hidden" name="parent_id" value="">
                  <button class="rounded bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">Save</button>
                </form>
              </details>

              {{-- Delete ROOT --}}
              <form action="{{ route('admin.categories.destroy', $root) }}" method="POST" class="inline ml-1 js-delete">
                @csrf @method('DELETE')
                <button class="rounded px-3 py-1 text-rose-600 hover:bg-rose-50">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="5" class="px-3 py-6 text-center text-gray-500">Belum ada kategori utama.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $roots->links() }}</div>
@endsection
