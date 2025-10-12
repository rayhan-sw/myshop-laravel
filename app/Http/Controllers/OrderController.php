<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // ================== USER (tetap) ==================
    public function index()
    {
        $orders = Order::with('items.product')
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        return Inertia::render('Orders/Index', [
            'orders' => $orders,
        ]);
    }

    public function checkoutForm()
    {
        $cart = Cart::where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        return Inertia::render('Orders/Checkout', [
            'cart'  => $cart,
            'total' => $cart->items->sum(fn ($i) => $i->qty * $i->product->price),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'address_text' => 'required|string|min:5',
        ]);

        $cart = Cart::where('user_id', Auth::id())
            ->with('items.product')
            ->firstOrFail();

        if ($cart->items->isEmpty()) {
            return back()->with('error', 'Keranjang kosong.');
        }

        DB::transaction(function () use ($cart, $request) {
            $total = 0;

            $order = Order::create([
                'user_id'      => Auth::id(),
                'total'        => 0,
                'status'       => 'pending',
                'address_text' => $request->address_text,
            ]);

            foreach ($cart->items as $item) {
                $subtotal = $item->qty * $item->product->price;
                $total   += $subtotal;

                OrderItem::create([
                    'order_id'   => $order->id,
                    'product_id' => $item->product->id,
                    'qty'        => $item->qty,
                    'price'      => $item->product->price,
                    'subtotal'   => $subtotal,
                ]);

                // Kurangi stok produk
                $item->product->decrement('stock', $item->qty);
            }

            $order->update(['total' => $total]);
            $cart->items()->delete();
        });

        return redirect()->route('orders.index')->with('success', 'Pesanan berhasil dibuat!');
    }

    // ================== ADMIN (Blade) ==================
    public function adminIndex()
    {
        // paginate(10) + relasi user & items.product
        $orders = Order::with(['user', 'items.product'])
            ->latest()
            ->paginate(10);

        // Blade admin
        return view('admin.orders.index', compact('orders'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,diproses,dikirim,selesai,batal',
        ]);

        // Jika sudah selesai, tidak boleh diubah selain tetap 'selesai'
        if ($order->status === 'selesai' && $request->status !== 'selesai') {
            return back()->with('warning', 'Order sudah selesai dan tidak dapat diubah.');
        }

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Status diperbarui!');
    }

    // Hapus riwayat (admin)
    public function destroy(Order $order)
    {
        // Hapus beserta itemnya (diasumsikan FK cascade sudah di migration)
        $order->delete();

        return back()->with('success', 'Riwayat order dihapus.');
    }
}
