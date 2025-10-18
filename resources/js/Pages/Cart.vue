<script setup>
import { Head, Link, router } from '@inertiajs/vue3'; // navigasi Inertia + Link
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { computed } from 'vue';

const props = defineProps({
    cart: { type: Object, required: true }, // data keranjang aktif
    total: { type: Number, required: true }, // total belanja (server-side)
});

const items = computed(() => props.cart?.items ?? []); // daftar item di keranjang

function money(n) {
    // format angka → Rupiah
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(Number(n || 0));
}

async function patchQty(itemId, nextQty) {
    // ubah qty item (PATCH ke server)
    const res = await fetch(route('cart.items.update', itemId), {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'), // token CSRF
        },
        body: JSON.stringify({ qty: nextQty }),
    });

    if (!res.ok) {
        // tampilkan error jika gagal
        const j = await res.json().catch(() => ({}));
        if (window.Swal)
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: j.message || 'Tidak bisa mengubah jumlah',
            });
        return;
    }
    window.location.reload(); // refresh agar sinkron dengan server
}

function inc(item) {
    // tambah qty (+1 hingga batas stok)
    const max = Number(item.product?.stock ?? 1);
    const next = Math.min(Number(item.qty) + 1, max);
    if (next === Number(item.qty)) {
        if (window.Swal)
            Swal.fire({
                icon: 'warning',
                title: 'Maksimal stok',
                text: 'Jumlah sudah mencapai stok tersedia.',
            });
        return;
    }
    patchQty(item.id, next);
}

function dec(item) {
    // kurangi qty (min 1)
    const next = Math.max(1, Number(item.qty) - 1);
    if (next === Number(item.qty)) return;
    patchQty(item.id, next);
}

async function removeItem(itemId) {
    // hapus 1 item dari keranjang
    const ok = await confirmSwal('Hapus produk ini dari cart?');
    if (!ok) return;

    const res = await fetch(route('cart.items.destroy', itemId), {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),
        },
    });

    if (!res.ok) {
        if (window.Swal)
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tidak bisa menghapus item',
            });
        return;
    }
    window.location.reload();
}

async function clearCart() {
    // kosongkan semua item
    const ok = await confirmSwal('Kosongkan semua item di cart?');
    if (!ok) return;

    const res = await fetch(route('cart.clear'), {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                .getAttribute('content'),
        },
    });

    if (!res.ok) {
        if (window.Swal)
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: 'Tidak bisa mengosongkan cart',
            });
        return;
    }
    window.location.reload();
}

function imgOf(p) {
    // pilih gambar utama produk → fallback placeholder
    const url = p?.images?.[0]?.url;
    if (url) return url;
    const idx = Number(p?.id) % 8 || 1; // placeholder lokal
    return `/theme/images/p${idx}.png`;
}

function confirmSwal(text) {
    // konfirmasi standar (SweetAlert/confirm)
    if (!window.Swal) return Promise.resolve(confirm(text));
    return Swal.fire({
        title: 'Konfirmasi',
        text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya',
        cancelButtonText: 'Batal',
    }).then((r) => r.isConfirmed);
}
</script>

<template>
    <SiteLayout>
        <Head title="Your Cart" />

        <section class="mx-auto max-w-7xl px-4 pb-10 pt-[100px]">
            <h1 class="text-2xl font-semibold text-brown dark:text-cream">
                Your Cart
            </h1>

            <div class="mt-6 grid gap-6 lg:grid-cols-[1fr,360px]">
                <!-- Items -->
                <div
                    class="overflow-hidden rounded-xl border bg-white dark:border-sage dark:bg-darkbrown/40"
                >
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 dark:bg-gray-800">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-brown dark:text-cream"
                                >
                                    Product
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-brown dark:text-cream"
                                >
                                    Price
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-brown dark:text-cream"
                                >
                                    Qty
                                </th>
                                <th
                                    class="px-4 py-3 text-left text-brown dark:text-cream"
                                >
                                    Subtotal
                                </th>
                                <th class="px-4 py-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="it in items"
                                :key="it.id"
                                class="border-t align-middle"
                            >
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <img
                                            :src="imgOf(it.product)"
                                            class="h-14 w-14 rounded border object-cover"
                                            alt=""
                                        />
                                        <div>
                                            <div class="font-medium">
                                                {{ it.product?.name }}
                                            </div>
                                            <div class="text-xs text-gray-500">
                                                {{
                                                    it.product?.category?.parent
                                                        ?.name
                                                        ? `${it.product.category.parent.name} → ${it.product.category.name}`
                                                        : it.product?.category
                                                              ?.name
                                                }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    {{ money(it.product?.price) }}
                                </td>
                                <td class="px-4 py-3">
                                    <div
                                        class="inline-flex items-center rounded-md border"
                                    >
                                        <button
                                            class="px-2 py-1"
                                            @click="dec(it)"
                                        >
                                            -
                                        </button>
                                        <input
                                            class="w-10 border-x bg-white px-2 py-1 text-center dark:bg-brown"
                                            :value="it.qty"
                                            disabled
                                        />
                                        <button
                                            class="px-2 py-1"
                                            @click="inc(it)"
                                        >
                                            +
                                        </button>
                                    </div>
                                    <div class="mt-1 text-xs text-gray-500">
                                        Stok: {{ it.product?.stock ?? 0 }}
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    {{
                                        money(
                                            (it.product?.price || 0) *
                                                (it.qty || 0),
                                        )
                                    }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    <button
                                        class="text-rose-600 hover:underline"
                                        @click="removeItem(it.id)"
                                    >
                                        Remove
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="!items.length">
                                <td
                                    colspan="5"
                                    class="px-4 py-10 text-center text-gray-500"
                                >
                                    Keranjang kosong.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <aside
                    class="rounded-xl border bg-white p-4 dark:bg-darkbrown/40"
                >
                    <div
                        class="bg-ctext-brown flex items-center justify-between dark:text-cream"
                    >
                        <div class="font-medium">Total</div>
                        <div class="text-lg font-semibold">
                            {{ money(total) }}
                        </div>
                    </div>

                    <!-- ke halaman checkout -->
                    <button
                        class="mt-3 w-full rounded-md border bg-sage px-4 py-2 hover:bg-sage/80"
                        :disabled="!items.length"
                        @click="router.visit(route('checkout.form'))"
                    >
                        Proceed to Checkout
                    </button>

                    <button
                        class="mt-3 w-full rounded-md border px-4 py-2 hover:bg-gray-50"
                        @click="clearCart"
                    >
                        Clear Cart
                    </button>

                    <p class="mt-3 text-xs text-gray-500">&nbsp;</p>
                </aside>
            </div>
        </section>
    </SiteLayout>
</template>
