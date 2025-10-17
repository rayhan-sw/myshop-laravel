@extends('admin.layouts.app')
@section('title','Dashboard')

@section('content')
<div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
    <h1 class="text-xl font-semibold">Dashboard</h1>
</div>

{{-- Cards --}}
<div class="grid gap-4 grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4">
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
<div class="mt-4 grid gap-4 md:grid-cols-1 lg:grid-cols-3">
    <div class="rounded-lg border bg-white p-4 lg:col-span-2">
        <div class="mb-2 font-medium">Sales (30 hari)</div>
        <div class="w-full relative" style="height: 15rem;">
          <canvas id="salesChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
    </div>
    <div class="rounded-lg border bg-white p-4">
        <div class="mb-2 font-medium">Status Orders</div>
        <div class="w-full relative" style="height: 15rem;">
            <canvas id="statusChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
    </div>
</div>

{{-- Popular Products & Top Categories --}}
<div class="mt-4 grid gap-4 md:grid-cols-1 lg:grid-cols-2">
    <div class="rounded-lg border bg-white p-4">
        <div class="mb-2 font-medium">Popular Products (qty)</div>
        <div class="w-full relative" style="height: 15rem;">
            <canvas id="popularChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
        <ul class="mt-3 space-y-1 text-sm text-gray-600">
            @foreach($popular as $pp)
                <li>{{ $pp->product->name ?? 'N/A' }} — {{ $pp->q }}</li>
            @endforeach
        </ul>
    </div>
    <div class="rounded-lg border bg-white p-4">
        <div class="mb-2 font-medium">Top Categories (qty)</div>
        <div class="w-full relative" style="height: 15rem;">
            <canvas id="categoryChart" class="absolute inset-0 w-full h-full"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const days   = @json(($chartDays ?? collect())->map(fn($d)=>date('d M', strtotime($d))));
const series = @json($salesSeries ?? []);

// LINE: Sales 30 hari
const salesChart = new Chart(document.getElementById('salesChart'), {
    type: 'line',
    data: {
        labels: days,
        datasets: [{
            label: 'Revenue (Rp)',
            data: series,
            tension: 0.35,
            fill: true,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + new Intl.NumberFormat('id-ID').format(v) } }
        }
    }
});

// DOUGHNUT: Status
const statusChart = new Chart(document.getElementById('statusChart'), {
    type: 'doughnut',
    data: {
        labels: ['Pending','Diproses','Dikirim','Selesai','Batal'],
        datasets: [{
            data: [{{ (int)($pending ?? 0) }}, {{ (int)($diproses ?? 0) }}, {{ (int)($dikirim ?? 0) }}, {{ (int)($selesai ?? 0) }}, {{ (int)($batal ?? 0) }}]
        }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
});

// BAR: Produk Terpopuler
const popularChart = new Chart(document.getElementById('popularChart'), {
    type: 'bar',
    data: {
        labels: @json(($popular ?? collect())->map(fn($p)=>$p->product->name ?? 'N/A')),
        datasets: [{ label: 'Qty', data: @json(($popular ?? collect())->pluck('q')) }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
});

// BAR: Kategori Teratas
const categoryChart = new Chart(document.getElementById('categoryChart'), {
    type: 'bar',
    data: {
        labels: @json(($topCategories ?? collect())->pluck('name')),
        datasets: [{ label: 'Qty', data: @json(($topCategories ?? collect())->pluck('q')) }]
    },
    options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } } }
});

// Resize chart saat window diubah ukuran
window.addEventListener('resize', () => {
    salesChart.resize();
    statusChart.resize();
    popularChart.resize();
    categoryChart.resize();
});



</script>
@endpush
