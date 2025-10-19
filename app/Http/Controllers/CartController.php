<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    // Ambil/buat keranjang milik user yang sedang login
    protected function userCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    public function index()
    {
        // Muat keranjang beserta relasi produk, gambar, dan kategori
        $cart = $this->userCart()->load([
            'items.product.images',
            'items.product.category.parent',
        ]);

        // Hitung total harga saat ini (price × qty)
        $total = $cart->items->sum(fn ($i) => (float) $i->product->price * (int) $i->qty);

        return Inertia::render('Cart', [
            'cart'  => $cart,
            'total' => $total,
        ]);
    }

    // Tambah item ke keranjang; validasi input, cek stok, gabungkan qty jika sudah ada
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['required','integer','min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qtyReq  = (int) $data['qty'];

        // Tolak jika stok kosong
        if ((int) $product->stock < 1) {
            return response()->json([
                'ok'      => false,
                'error'   => 'out_of_stock',
                'message' => 'Stok produk kosong.',
            ], 422);
        }

        $cart = $this->userCart();
        $item = $cart->items()->where('product_id', $product->id)->first();

        $current = (int) ($item?->qty ?? 0); // qty yang sudah ada di keranjang
        $newQty  = $current + $qtyReq;       // qty setelah penambahan
        $max     = (int) $product->stock;    // batas stok

        // Batasi agar tidak melebihi stok
        if ($newQty > $max) {
            return response()->json([
                'ok'       => false,
                'error'    => 'qty_exceeds_stock',
                'message'  => "Stok tersisa {$max}. Di keranjang sudah {$current}.",
                'max'      => $max,
                'in_cart'  => $current,
                'attempt'  => $qtyReq,
            ], 422);
        }

        // Update jika sudah ada, atau buat item baru
        if ($item) {
            $item->update(['qty' => $newQty]);
        } else {
            $item = $cart->items()->create([
                'product_id' => $product->id,
                'qty'        => $qtyReq,
            ]);
        }

        return response()->json([
            'ok'       => true,
            'item_id'  => $item->id,
            'item_qty' => (int) $item->qty,
            'stock'    => $max,
        ], 201);
    }

    // Ubah qty item; pastikan milik user dan tidak melebihi stok
    public function update(Request $request, CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) abort(403); // proteksi kepemilikan

        $data = $request->validate([
            'qty' => ['required','integer','min:1'],
        ]);

        $max    = (int) $item->product->stock;
        $newQty = (int) $data['qty'];

        // Validasi batas stok
        if ($newQty > $max) {
            return response()->json([
                'ok'      => false,
                'error'   => 'qty_exceeds_stock',
                'message' => "Maksimum stok {$max}.",
                'max'     => $max,
            ], 422);
        }

        $item->update(['qty' => $newQty]);

        return response()->json([
            'ok'       => true,
            'item_id'  => $item->id,
            'item_qty' => (int) $item->qty,
        ]);
    }

    // Hapus satu item dari keranjang
    public function destroy(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) abort(403); // proteksi kepemilikan

        $item->delete();
        return response()->json(['ok' => true]);
    }

    // Kosongkan seluruh item pada keranjang user
    public function clear()
    {
        $cart = $this->userCart();
        $cart->items()->delete();

        return response()->json(['ok' => true]);
    }
}
