@extends('admin.layouts.app')

@section('content')
  <div class="mb-6">
    <h1 class="text-2xl font-semibold">Subcategories of: <span class="text-indigo-700">{{ $parent->name }}</span></h1>
    <div class="mt-2">
      <a href="{{ route('admin.categories.index') }}" class="text-sm text-gray-600 hover:text-gray-800">← Back to Root Categories</a>
    </div>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">{{ session('success') }}</div>
  @endif
  @if(session('error'))
    <div class="mb-4 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">{{ session('error') }}</div>
  @endif

  {{-- Form tambah SUB untuk parent ini --}}
  <div class="mb-6 rounded-lg border bg-white p-4">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="flex flex-col gap-3 sm:flex-row">
      @csrf
      <input type="text" name="name" value="{{ old('name') }}" class="w-full rounded-md border px-3 py-2" placeholder="Nama subkategori (contoh: Rok, Blouse, Kemeja)" required maxlength="100">
      <input type="hidden" name="parent_id" value="{{ $parent->id }}">
      <button class="rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">Add Subcategory</button>
    </form>
    @error('name')<p class="mt-2 text-sm text-rose-600">{{ $message }}</p>@enderror
  </div>

  {{-- Tabel SUB --}}
  <div class="overflow-x-auto rounded-lg border bg-white">
    <table class="min-w-[640px] w-full text-left text-sm">
      <thead class="border-b bg-gray-50 text-gray-600">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">Name</th>
          <th class="px-3 py-2">Created at</th>
          <th class="px-3 py-2 text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        @forelse($subs as $sub)
          <tr class="border-b last:border-0">
            <td class="px-3 py-2">{{ $loop->iteration + (($subs->currentPage()-1) * $subs->perPage()) }}</td>
            <td class="px-3 py-2">{{ $sub->name }}</td>
            <td class="px-3 py-2">{{ $sub->created_at?->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2 text-right">
              {{-- Edit SUB --}}
              <details class="inline-block text-left align-middle">
                <summary class="inline cursor-pointer rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50">Edit</summary>
                <form method="POST" action="{{ route('admin.categories.update', $sub) }}" class="mt-2 flex items-center gap-2 js-edit">
                  @csrf @method('PUT')
                  <input type="text" name="name" value="{{ old('name',$sub->name) }}" class="w-56 rounded-md border px-3 py-1" required maxlength="100">
                  {{-- tetap di parent yg sama --}}
                  <input type="hidden" name="parent_id" value="{{ $parent->id }}">
                  <button class="rounded bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">Save</button>
                </form>
              </details>

              {{-- Delete SUB --}}
              <form action="{{ route('admin.categories.destroy', $sub) }}" method="POST" class="inline ml-1 js-delete">
                @csrf @method('DELETE')
                <button class="rounded px-3 py-1 text-rose-600 hover:bg-rose-50">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr><td colspan="4" class="px-3 py-6 text-center text-gray-500">Belum ada subkategori.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">{{ $subs->links() }}</div>
@endsection
