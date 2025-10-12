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
    /** Ambil/buat cart milik user yang login */
    protected function userCart(): Cart
    {
        return Cart::firstOrCreate(['user_id' => Auth::id()]);
    }

    public function index()
    {
        $cart = $this->userCart()->load([
            'items.product.images',
            'items.product.category.parent',
        ]);

        $total = $cart->items->sum(fn ($i) => (float) $i->product->price * (int) $i->qty);

        return Inertia::render('Cart', [
            'cart'  => $cart,
            'total' => $total,
        ]);
    }

    /** Tambah item ke cart (validasi JSON, batasi stok & gabung dengan qty yang sudah ada) */
    public function store(Request $request)
    {
        $data = $request->validate([
            'product_id' => ['required','integer','exists:products,id'],
            'qty'        => ['required','integer','min:1'],
        ]);

        $product = Product::findOrFail($data['product_id']);
        $qtyReq  = (int) $data['qty'];

        if ((int) $product->stock < 1) {
            return response()->json([
                'ok'      => false,
                'error'   => 'out_of_stock',
                'message' => 'Stok produk kosong.',
            ], 422);
        }

        $cart = $this->userCart();
        $item = $cart->items()->where('product_id', $product->id)->first();

        $current = (int) ($item?->qty ?? 0);
        $newQty  = $current + $qtyReq;
        $max     = (int) $product->stock;

        if ($newQty > $max) {
            // kirim 422 agar front-end bisa munculkan SweetAlert (tanpa redirect)
            return response()->json([
                'ok'       => false,
                'error'    => 'qty_exceeds_stock',
                'message'  => "Stok tersisa {$max}. Di keranjang sudah {$current}.",
                'max'      => $max,
                'in_cart'  => $current,
                'attempt'  => $qtyReq,
            ], 422);
        }

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

    /** Ubah qty (wajib <= stok) */
    public function update(Request $request, CartItem $item)
    {
        // pastikan item milik user
        if ($item->cart->user_id !== Auth::id()) abort(403);

        $data = $request->validate([
            'qty' => ['required','integer','min:1'],
        ]);

        $max    = (int) $item->product->stock;
        $newQty = (int) $data['qty'];

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

    /** Hapus satu item */
    public function destroy(CartItem $item)
    {
        if ($item->cart->user_id !== Auth::id()) abort(403);

        $item->delete();
        return response()->json(['ok' => true]);
    }

    /** Kosongkan cart */
    public function clear()
    {
        $cart = $this->userCart();
        $cart->items()->delete();

        return response()->json(['ok' => true]);
    }
}
