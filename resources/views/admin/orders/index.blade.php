{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.app')

@section('title', 'Orders')

@section('content')
<div class="card shadow-sm">
  <div class="card-body">
    <h5 class="card-title mb-4 fw-bold text-indigo-600">
       Orders Management
    </h5>

    {{-- Flash messages --}}
    @if(session('success'))  <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('warning'))  <div class="alert alert-warning">{{ session('warning') }}</div> @endif
    @if($errors->any())      <div class="alert alert-danger">{{ $errors->first() }}</div> @endif

    {{-- Tabs status (client-side, tanpa route baru) --}}
    @php
      $statuses = [
        'all' => 'Semua',
        'pending' => 'Pending',
        'diproses' => 'Diproses',
        'dikirim' => 'Dikirim',
        'selesai' => 'Selesai',
        'batal' => 'Batal'
      ];
    @endphp

    {{-- ======= STATUS TABS ======= --}}
    <div class="status-tabs-wrapper mb-4">
      <ul class="nav nav-pills justify-content-start align-items-center gap-2 flex-nowrap overflow-auto pb-2" id="ordersTabs">
        @foreach($statuses as $key => $label)
          <li class="nav-item">
            <a class="nav-link fw-medium py-2 px-4 rounded-pill"
               href="#" data-tab="{{ $key }}">
              {{ $label }}
            </a>
          </li>
        @endforeach
      </ul>
    </div>
    {{-- ======= END STATUS TABS ======= --}}

    {{-- Table --}}
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0 orders-table">
        <thead class="table-light sticky-top" style="top:0; z-index:5;">
          <tr class="text-nowrap">
            <th>#</th>
            <th>User</th>
            <th>Total</th>
            <th>Status</th>
            <th>Alamat</th>
            <th>Waktu</th>
            <th>Items</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody id="ordersBody">
          @forelse ($orders as $order)
            @php
              $status = $order->status;
              $badgeMap = [
                'pending'  => 'bg-secondary',
                'diproses' => 'bg-warning text-dark',
                'dikirim'  => 'bg-info text-dark',
                'selesai'  => 'bg-success',
                'batal'    => 'bg-danger',
              ];
              $badgeClass = $badgeMap[$status] ?? 'bg-secondary';
            @endphp
            <tr data-status="{{ $status }}">
              <td class="fw-semibold">#{{ $order->id }}</td>
              <td>
                <div class="fw-semibold">{{ $order->user?->name ?? '-' }}</div>
                <div class="text-muted small">{{ $order->user?->email }}</div>
              </td>
              <td class="fw-semibold">Rp {{ number_format($order->total ?? 0, 0, ',', '.') }}</td>

              <td>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                  <span class="badge {{ $badgeClass }} px-2">{{ ucfirst($status) }}</span>
                  @if($status !== 'selesai')
                    <form action="{{ route('admin.orders.update', $order) }}"
                          method="POST"
                          class="d-flex gap-2 align-items-center js-update-form mb-0">
                      @csrf
                      @method('PATCH')
                      <select name="status" class="form-select form-select-sm w-auto">
                        <option value="pending"  @selected($status==='pending')>Pending</option>
                        <option value="diproses" @selected($status==='diproses')>Diproses</option>
                        <option value="dikirim"  @selected($status==='dikirim')>Dikirim</option>
                        <option value="selesai"  @selected($status==='selesai')>Selesai</option>
                        <option value="batal"    @selected($status==='batal')>Batal</option>
                      </select>
                      <button type="submit" class="btn btn-primary btn-sm px-3">Update</button>
                    </form>
                  @endif
                </div>
              </td>

              <td class="text-wrap" style="max-width:240px;">{{ $order->address_text ?: '—' }}</td>
              <td>
                <div>{{ $order->created_at?->format('d/m/Y H:i') }}</div>
                <div class="text-muted small">{{ $order->created_at?->diffForHumans() }}</div>
              </td>

              <td class="text-wrap" style="max-width:300px;">
                @foreach($order->items as $it)
                  <div class="d-flex justify-content-between border-bottom py-1">
                    <div><strong>{{ $it->product?->name ?? '—' }}</strong> ×{{ $it->qty }}</div>
                    <div>Rp {{ number_format($it->subtotal ?? (($it->price ?? 0) * ($it->qty ?? 0)), 0, ',', '.') }}</div>
                  </div>
                @endforeach
              </td>

              <td class="text-end">
                @if($order->status === 'selesai')
                  <form action="{{ route('admin.orders.destroy', $order) }}"
                        method="POST"
                        class="d-inline js-delete mb-0">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-danger btn-sm px-3">Hapus</button>
                  </form>
                @else
                  <span class="text-muted small">Update status untuk tindakan lain</span>
                @endif
              </td>
            </tr>
          @empty
            <tr><td colspan="8" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="d-flex justify-content-end mt-3">
      {{ $orders->links() }}
    </div>
  </div>
</div>
@endsection

@push('styles')
<style>
  /* === STATUS TABS === */
  #ordersTabs {
    list-style: none;
    margin: 0;
    padding: 0;
    display: flex;
    flex-direction: row;
    flex-wrap: nowrap;
    overflow-x: auto;
    scrollbar-width: thin;
    scrollbar-color: #c7d2fe transparent;
  }
  #ordersTabs::-webkit-scrollbar {
    height: 6px;
  }
  #ordersTabs::-webkit-scrollbar-thumb {
    background: #c7d2fe;
    border-radius: 3px;
  }
  #ordersTabs .nav-item {
    display: inline-block;
  }
  #ordersTabs .nav-link {
    color: #4f46e5;
    border: 1px solid #e5e7eb;
    background: #f9fafb;
    transition: all 0.2s ease-in-out;
    white-space: nowrap;
  }
  #ordersTabs .nav-link:hover {
    background: #ede9fe;
    color: #4338ca;
    border-color: #c7d2fe;
  }
  #ordersTabs .nav-link.active {
    background: #4f46e5;
    color: #fff !important;
    border-color: #4f46e5;
    box-shadow: 0 2px 6px rgba(79,70,229,0.25);
  }

  /* === TABLE === */
  .orders-table td, .orders-table th { vertical-align: middle; }
  .orders-table tbody tr + tr { border-top: 1px solid rgba(0,0,0,.05); }
  .table-hover tbody tr:hover { background: #f9fafb; }

  /* === BUTTONS === */
  .btn-primary {
    background-color: #4f46e5 !important;
    border-color: #4f46e5 !important;
  }
  .btn-primary:hover {
    background-color: #4338ca !important;
    border-color: #4338ca !important;
  }
  .btn-danger {
    background-color: #ef4444 !important;
    border-color: #ef4444 !important;
    color: #fff !important;
  }
  .btn-danger:hover {
    background-color: #dc2626 !important;
    border-color: #dc2626 !important;
  }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', () => {
  const KEY = 'orders_active_tab';
  const tabs = document.querySelectorAll('#ordersTabs .nav-link');
  const tbody = document.getElementById('ordersBody');

  function setActiveTab(name) {
    tabs.forEach(t => t.classList.toggle('active', t.dataset.tab === name));
    const rows = tbody.querySelectorAll('tr[data-status]');
    rows.forEach(r => {
      const st = r.getAttribute('data-status');
      r.style.display = (name === 'all' || st === name) ? '' : 'none';
    });
    localStorage.setItem(KEY, name);
  }

  const initial = localStorage.getItem(KEY) || 'all';
  setActiveTab(initial);
  tabs.forEach(t => t.addEventListener('click', e => {
    e.preventDefault();
    setActiveTab(t.dataset.tab);
  }));

  // Konfirmasi hapus
  document.querySelectorAll('form.js-delete').forEach(f => {
    f.addEventListener('submit', e => {
      e.preventDefault();
      Swal.fire({
        title: 'Hapus order ini?',
        text: 'Tindakan ini tidak bisa dibatalkan.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal',
        confirmButtonColor: '#ef4444'
      }).then(res => res.isConfirmed && f.submit());
    });
  });
});
</script>
@endpush
