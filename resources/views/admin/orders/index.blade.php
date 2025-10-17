@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="mb-6">
  <h1 class="text-2xl font-semibold text-indigo-600 mb-2">Orders Management</h1>

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="mb-3 rounded border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-800">
      {{ session('success') }}
    </div>
  @endif
  @if(session('warning'))
    <div class="mb-3 rounded border border-yellow-200 bg-yellow-50 px-3 py-2 text-sm text-yellow-800">
      {{ session('warning') }}
    </div>
  @endif
  @if($errors->any())
    <div class="mb-3 rounded border border-rose-200 bg-rose-50 px-3 py-2 text-sm text-rose-800">
      {{ $errors->first() }}
    </div>
  @endif

  {{-- Status tabs --}}
  @php
    $statuses = ['all'=>'Semua','pending'=>'Pending','diproses'=>'Diproses','dikirim'=>'Dikirim','selesai'=>'Selesai','batal'=>'Batal'];
  @endphp
  <div class="mb-4 overflow-x-auto">
    <ul class="flex gap-2 whitespace-nowrap" id="ordersTabs">
      @foreach($statuses as $key => $label)
        <li>
          <a href="#" data-tab="{{ $key }}"
             class="inline-block px-4 py-2 rounded-full text-indigo-600 border border-gray-200 bg-gray-50 hover:bg-gray-100 transition">
            {{ $label }}
          </a>
        </li>
      @endforeach
    </ul>
  </div>

  {{-- Table --}}
  <div class="overflow-x-auto rounded-lg border bg-white">
    <table class="min-w-full w-full text-left text-sm orders-table">
      <thead class="bg-gray-50 text-gray-600 border-b sticky top-0 z-10">
        <tr>
          <th class="px-3 py-2">#</th>
          <th class="px-3 py-2">User</th>
          <th class="px-3 py-2">Total</th>
          <th class="px-3 py-2">Status</th>
          <th class="px-3 py-2">Alamat</th>
          <th class="px-3 py-2">Waktu</th>
          <th class="px-3 py-2">Items</th>
          <th class="px-3 py-2 text-right">Aksi</th>
        </tr>
      </thead>
      <tbody id="ordersBody">
        @forelse ($orders as $order)
          @php
            $status = $order->status;
            $badgeMap = [
              'pending'=>'bg-gray-200 text-gray-800',
              'diproses'=>'bg-yellow-200 text-yellow-800',
              'dikirim'=>'bg-blue-200 text-blue-800',
              'selesai'=>'bg-green-200 text-green-800',
              'batal'=>'bg-red-200 text-red-800',
            ];
            $badgeClass = $badgeMap[$status] ?? 'bg-gray-200';
          @endphp
          <tr data-status="{{ $status }}" class="hover:bg-gray-50 transition">
            <td class="px-3 py-2 font-medium">#{{ $order->id }}</td>
            <td class="px-3 py-2">
              <div class="font-medium">{{ $order->user?->name ?? '-' }}</div>
              <div class="text-gray-500 text-xs">{{ $order->user?->email }}</div>
            </td>
            <td class="px-3 py-2 font-medium">Rp {{ number_format($order->total ?? 0,0,',','.') }}</td>
            <td class="px-3 py-2">
              <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                <span class="px-2 py-1 rounded {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                @if($status !== 'selesai')
                  <form action="{{ route('admin.orders.update', $order) }}" method="POST" class="flex flex-col sm:flex-row sm:items-center gap-2">
                    @csrf @method('PATCH')
                    <select name="status" class="rounded border px-2 py-1 text-sm w-full sm:w-auto">
                      @foreach($statuses as $key => $label)
                        <option value="{{ $key }}" @selected($status===$key)>{{ $label }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="rounded bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-1 text-sm">Update</button>
                  </form>
                @endif
              </div>
            </td>
            <td class="px-3 py-2 max-w-xs truncate">{{ $order->address_text ?: '—' }}</td>
            <td class="px-3 py-2">
              <div>{{ $order->created_at?->format('d/m/Y H:i') }}</div>
              <div class="text-gray-500 text-xs">{{ $order->created_at?->diffForHumans() }}</div>
            </td>
            <td class="px-3 py-2 max-w-xs">
              @foreach($order->items as $it)
                <div class="flex justify-between border-b py-1">
                  <div><strong>{{ $it->product?->name ?? '—' }}</strong> ×{{ $it->qty }}</div>
                  <div>Rp {{ number_format($it->subtotal ?? (($it->price ?? 0)*($it->qty ?? 0)),0,',','.') }}</div>
                </div>
              @endforeach
            </td>
            <td class="px-3 py-2 text-right">
              @if($order->status === 'selesai')
                <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" class="inline js-delete">
                  @csrf @method('DELETE')
                  <button class="rounded bg-red-600 hover:bg-red-700 text-white px-3 py-1 text-sm">Hapus</button>
                </form>
              @else
                <span class="text-gray-500 text-xs">Update status untuk tindakan lain</span>
              @endif
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="px-3 py-6 text-center text-gray-500">Belum ada pesanan.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>

  {{-- Pagination --}}
  <div class="mt-4 flex justify-end">
    {{ $orders->links() }}
  </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const KEY = 'orders_active_tab';
  const tabs = document.querySelectorAll('#ordersTabs a');
  const tbody = document.getElementById('ordersBody');

  function setActiveTab(name) {
    tabs.forEach(t => t.classList.toggle('bg-indigo-600 text-white border-indigo-600', t.dataset.tab === name));
    const rows = tbody.querySelectorAll('tr[data-status]');
    rows.forEach(r => {
      const st = r.getAttribute('data-status');
      r.style.display = (name==='all' || st===name) ? '' : 'none';
    });
    localStorage.setItem(KEY, name);
  }

  setActiveTab(localStorage.getItem(KEY) || 'all');
  tabs.forEach(t => t.addEventListener('click', e => {
    e.preventDefault();
    setActiveTab(t.dataset.tab);
  }));

  // Konfirmasi hapus
  document.querySelectorAll('form.js-delete').forEach(f=>{
    f.addEventListener('submit', e=>{
      e.preventDefault();
      Swal.fire({
        title: 'Hapus order ini?',
        text: 'Tindakan ini tidak bisa dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
      }).then(res=>res.isConfirmed && f.submit());
    });
  });
});
</script>
@endpush
