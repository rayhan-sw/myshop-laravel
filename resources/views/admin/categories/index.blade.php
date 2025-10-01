@extends('admin.layouts.app')

@section('content')
  <div class="mb-6 flex items-center justify-between">
    <h1 class="text-2xl font-semibold">Categories</h1>
  </div>

  @if(session('success'))
    <div class="mb-4 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
      {{ session('success') }}
    </div>
  @endif

  {{-- Form tambah kategori --}}
  <div class="mb-6 rounded-lg border bg-white p-4">
    <form action="{{ route('admin.categories.store') }}" method="POST" class="flex flex-col gap-3 sm:flex-row">
      @csrf
      <input
        type="text"
        name="name"
        value="{{ old('name') }}"
        class="w-full rounded-md border px-3 py-2"
        placeholder="New category name…"
        required
      >
      <button class="btn-primary rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700">
        Add
      </button>
    </form>
    @error('name')
      <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
    @enderror
  </div>

  {{-- Tabel kategori --}}
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
        @forelse($categories as $i => $cat)
          <tr class="border-b last:border-0">
            <td class="px-3 py-2">
              {{ $loop->iteration + (($categories->currentPage() - 1) * $categories->perPage()) }}
            </td>
            <td class="px-3 py-2">{{ $cat->name }}</td>
            <td class="px-3 py-2">{{ $cat->created_at?->format('Y-m-d H:i') }}</td>
            <td class="px-3 py-2 text-right">
              {{-- Edit inline (tanpa JS) --}}
              <details class="inline-block text-left align-middle">
                <summary class="inline cursor-pointer rounded px-3 py-1 text-indigo-600 hover:bg-indigo-50">
                  Edit
                </summary>
                <form method="POST" action="{{ route('admin.categories.update', $cat) }}" class="mt-2 flex items-center gap-2">
                  @csrf
                  @method('PUT')
                  <input
                    type="text"
                    name="name"
                    value="{{ old('name', $cat->name) }}"
                    class="w-56 rounded-md border px-3 py-1"
                    maxlength="100"
                    required
                  >
                  <button class="rounded bg-indigo-600 px-3 py-1.5 text-white hover:bg-indigo-700">
                    Save
                  </button>
                </form>
              </details>

              {{-- Delete --}}
              <form
                action="{{ route('admin.categories.destroy', $cat) }}"
                method="POST"
                onsubmit="return confirm('Delete this category?')"
                class="inline ml-1"
              >
                @csrf
                @method('DELETE')
                <button class="rounded px-3 py-1 text-rose-600 hover:bg-rose-50">Delete</button>
              </form>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="4" class="px-3 py-6 text-center text-gray-500">No categories yet.</td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>

  <div class="mt-4">
    {{ $categories->links() }}
  </div>
@endsection
