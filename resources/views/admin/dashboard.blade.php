@extends('admin.layouts.app')
@section('title','Dashboard')

@section('content')
  <div class="mb-4 flex items-center justify-between">
    <h1 class="text-xl font-semibold">Dashboard</h1>
  </div>

  {{-- Cards --}}
  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Total Income</div>
      <div class="mt-1 text-2xl font-bold">Rp {{ number_format($income ?? 0, 0, ',', '.') }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Total Orders</div>
      <div class="mt-1 text-2xl font-bold">{{ $totalOrders ?? 0 }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Pending</div>
      <div class="mt-1 text-2xl font-bold">{{ $pending ?? 0 }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Diproses</div>
      <div class="mt-1 text-2xl font-bold">{{ $diproses ?? 0 }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Dikirim</div>
      <div class="mt-1 text-2xl font-bold">{{ $dikirim ?? 0 }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Selesai</div>
      <div class="mt-1 text-2xl font-bold">{{ $selesai ?? 0 }}</div>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="text-xs text-gray-500">Batal</div>
      <div class="mt-1 text-2xl font-bold">{{ $batal ?? 0 }}</div>
    </div>
  </div>

  {{-- Charts --}}
  <div class="mt-4 grid gap-4 lg:grid-cols-3">
    <div class="rounded-lg border bg-white p-4 lg:col-span-2">
      <div class="mb-2 font-medium">Sales (30 hari)</div>
      <canvas id="salesChart" height="120"></canvas>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="mb-2 font-medium">Status Orders</div>
      <canvas id="statusChart" height="120"></canvas>
    </div>
  </div>

  <div class="mt-4 grid gap-4 lg:grid-cols-2">
    <div class="rounded-lg border bg-white p-4">
      <div class="mb-2 font-medium">Popular Products (qty)</div>
      <canvas id="popularChart" height="120"></canvas>
      <ul class="mt-3 space-y-1 text-sm text-gray-600">
        @foreach($popular as $pp)
          <li>{{ $pp->product->name ?? 'N/A' }} — {{ $pp->q }}</li>
        @endforeach
      </ul>
    </div>
    <div class="rounded-lg border bg-white p-4">
      <div class="mb-2 font-medium">Top Categories (qty)</div>
      <canvas id="categoryChart" height="120"></canvas>
    </div>
  </div>
@endsection

@push('scripts')
  {{-- Chart.js CDN (tanpa custom colors agar serasi tema) --}}
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
  <script>
    const days   = @json(($chartDays ?? collect())->map(fn($d)=>date('d M', strtotime($d))));
    const series = @json($salesSeries ?? []);

    // LINE: Sales 30 hari
    new Chart(document.getElementById('salesChart'), {
      type: 'line',
      data: {
        labels: days,
        datasets: [{
          label: 'Revenue (Rp)',
          data: series,
          tension: .35,
          fill: true,
        }]
      },
      options: {
        plugins: { legend: { display: false }},
        scales: {
          y: {
            ticks: {
              callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v)
            }
          }
        }
      }
    });

    // DOUGHNUT: Status
    new Chart(document.getElementById('statusChart'), {
      type: 'doughnut',
      data: {
        labels: ['Pending','Diproses','Dikirim','Selesai','Batal'],
        datasets: [{
          data: [{{ (int)($pending ?? 0) }}, {{ (int)($diproses ?? 0) }}, {{ (int)($dikirim ?? 0) }}, {{ (int)($selesai ?? 0) }}, {{ (int)($batal ?? 0) }}]
        }]
      },
      options: { plugins: { legend: { position: 'bottom' } } }
    });

    // BAR: Produk Terpopuler
    new Chart(document.getElementById('popularChart'), {
      type: 'bar',
      data: {
        labels: @json(($popular ?? collect())->map(fn($p)=>$p->product->name ?? 'N/A')),
        datasets: [{ label: 'Qty', data: @json(($popular ?? collect())->pluck('q')) }]
      },
      options: { plugins: { legend: { display: false } } }
    });

    // BAR: Kategori Teratas
    new Chart(document.getElementById('categoryChart'), {
      type: 'bar',
      data: {
        labels: @json(($topCategories ?? collect())->pluck('name')),
        datasets: [{ label: 'Qty', data: @json(($topCategories ?? collect())->pluck('q')) }]
      },
      options: { plugins: { legend: { display: false } } }
    });
  </script>
@endpush