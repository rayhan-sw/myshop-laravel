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
        // Statistik umum
        $totalOrders     = Order::count();
        $totalProducts   = Product::count();
        $totalCategories = Category::count();
        $totalUsers      = User::count();
        $income          = (float) Order::where('status', 'selesai')->sum('total');

        // Jumlah pesanan berdasarkan status
        $statusCounts = Order::select('status', DB::raw('COUNT(*) as c'))
            ->groupBy('status')
            ->pluck('c', 'status');

        $pending  = (int) ($statusCounts['pending']  ?? 0);
        $diproses = (int) ($statusCounts['diproses'] ?? 0);
        $dikirim  = (int) ($statusCounts['dikirim']  ?? 0);
        $selesai  = (int) ($statusCounts['selesai']  ?? 0);
        $batal    = (int) ($statusCounts['batal']    ?? 0);

        // Penjualan 30 hari terakhir (untuk grafik)
        $salesDaily = Order::where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->select(DB::raw('DATE(created_at) as d'), DB::raw('SUM(total) as s'))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $chartDays = collect(range(0, 29))
            ->map(fn($i) => now()->subDays(29 - $i)->format('Y-m-d'));

        $salesMap    = $salesDaily->keyBy('d')->map(fn($r) => (float) $r->s);
        $salesSeries = $chartDays->map(fn($d) => (float) ($salesMap[$d] ?? 0))->values();

        // Produk terpopuler (berdasarkan total terjual)
        $popular = OrderItem::select('product_id', DB::raw('SUM(qty) as q'))
            ->groupBy('product_id')
            ->orderByDesc('q')
            ->with('product')
            ->take(6)
            ->get();

        // Kategori teratas (berdasarkan jumlah produk terjual)
        $topCategories = OrderItem::join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->select('categories.name', DB::raw('SUM(order_items.qty) as q'))
            ->groupBy('categories.name')
            ->orderByDesc('q')
            ->take(6)
            ->get();

        // Kirim data ke view dashboard
        return view('admin.dashboard', [
            'totalOrders'     => $totalOrders,
            'totalProducts'   => $totalProducts,
            'totalCategories' => $totalCategories,
            'totalUsers'      => $totalUsers,
            'income'          => $income,
            'pending'         => $pending,
            'diproses'        => $diproses,
            'dikirim'         => $dikirim,
            'selesai'         => $selesai,
            'batal'           => $batal,
            'chartDays'       => $chartDays,
            'salesSeries'     => $salesSeries,
            'popular'         => $popular,
            'topCategories'   => $topCategories,
        ]);
    }
}
