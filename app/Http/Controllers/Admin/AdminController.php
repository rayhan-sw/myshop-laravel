<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        // === METRIK DASAR ===
        $totalOrders     = Order::count();
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $totalUsers      = User::count();
        $income          = (float) Order::where('status', 'selesai')->sum('total');

        // === STATUS PESANAN ===
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        $pending  = (int) ($statusCounts['pending']  ?? 0);
        $diproses = (int) ($statusCounts['diproses'] ?? 0);
        $dikirim  = (int) ($statusCounts['dikirim']  ?? 0);
        $selesai  = (int) ($statusCounts['selesai']  ?? 0);
        $batal    = (int) ($statusCounts['batal']    ?? 0);

        // === PENJUALAN 30 HARI TERAKHIR ===
        $salesDaily = Order::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(total) as s'))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        // siapkan urutan hari agar tidak bolong
        $chartDays = collect(range(0, 29))->map(fn($i) => now()->subDays(29 - $i)->format('Y-m-d'));
        $salesMap = $salesDaily->keyBy('d')->map(fn($r) => (float) $r->s);
        $salesSeries = $chartDays->map(fn($d) => (float) ($salesMap[$d] ?? 0))->values();

        // === PRODUK POPULER (berdasarkan jumlah terjual) ===
        $popular = OrderItem::select('product_id', DB::raw('SUM(qty) as q'))
            ->groupBy('product_id')
            ->orderByDesc('q')
            ->with('product')
            ->take(6)
            ->get();

        // === KATEGORI TERATAS ===
        $topCategories = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.qty) as q'))
            ->groupBy('categories.name')
            ->orderByDesc('q')
            ->take(6)
            ->get();

        // === KIRIM KE VIEW ===
        return view('admin.dashboard', [
            // Data umum
            'totalOrders'     => $totalOrders,
            'totalProducts'   => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalUsers'      => $totalUsers,
            'income'          => $income,

            // Status
            'pending'         => $pending,
            'diproses'        => $diproses,
            'dikirim'         => $dikirim,
            'selesai'         => $selesai,
            'batal'           => $batal,

            // Grafik
            'chartDays'       => $chartDays,
            'salesSeries'     => $salesSeries,

            // Daftar populer
            'popular'         => $popular,
            'topCategories'   => $topCategories,
        ]);
    }
}
