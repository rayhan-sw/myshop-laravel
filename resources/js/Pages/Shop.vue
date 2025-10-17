<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// Props dari backend
const page = usePage();
const products = page.props.products?.data ?? [];
const links = page.props.products?.links ?? [];
const roots = page.props.roots ?? [];
const filters = page.props.filters ?? { q: '', root_id: '', sub_id: '' };

// STATE filter
const selectedRootId = ref(filters.root_id ?? '');
const selectedSubId = ref(filters.sub_id ?? '');
watch(selectedRootId, () => (selectedSubId.value = ''));

// util kategori
const subsOf = (rootId) =>
    roots.find((r) => String(r.id) === String(rootId))?.children ?? [];
const allSubs = computed(() => roots.flatMap((r) => r.children ?? []));
const subsList = computed(() =>
    selectedRootId.value ? subsOf(selectedRootId.value) : allSubs.value,
);

// util tampilan
const imgOf = (p) => {
    const url = p?.images?.[0]?.url;
    if (url) return url;
    const idx = Number(p?.id) % 8 || 1;
    return `/theme/images/p${idx}.png`;
};
const categoryPath = (p) => {
    const cat = p?.category;
    if (!cat) return '';
    return cat?.parent?.name ? `${cat.parent.name} → ${cat.name}` : cat.name;
};

// === NEW badge helper (≤ 14 hari)
const isNew = (p, days = 14) => {
    const d = p?.created_at ? new Date(p.created_at) : null;
    if (!d || isNaN(d)) return false;
    const diffDays = (Date.now() - d.getTime()) / 86400000;
    return diffDays <= days;
};

// link detail
const showHref = (p) => route('product.show', p.slug ?? p.id);

// Add to cart
async function addToCart(productId, qty = 1) {
    const res = await fetch('/cart/items', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document
                .querySelector('meta[name="csrf-token"]')
                ?.getAttribute('content'),
        },
        credentials: 'same-origin',
        body: JSON.stringify({ product_id: productId, qty }),
    });

    if (res.ok) {
        window.Swal?.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: 'Ditambahkan ke cart',
            timer: 1200,
            showConfirmButton: false,
        });
        return;
    }
    let payload = null;
    try {
        payload = await res.json();
    } catch {}
    if (res.status === 401) window.location.href = route('login');
    else if (res.status === 419) {
        window.Swal?.fire({
            icon: 'warning',
            title: 'Sesi kedaluwarsa',
            text: 'Silakan muat ulang halaman.',
        }).then(() => window.location.reload());
    } else if (res.status === 422 && payload?.error === 'qty_exceeds_stock') {
        window.Swal?.fire({
            icon: 'warning',
            title: 'Stok tidak cukup',
            html: payload?.message || 'Jumlah melebihi stok.',
        });
    } else {
        window.Swal?.fire({
            icon: 'error',
            title: 'Gagal menambah',
            text: payload?.message || 'Terjadi kesalahan.',
        });
    }
}
</script>

<template>
    <SiteLayout>
        <Head title="Shop" />

        <section class="mx-auto max-w-7xl px-4 pt-[100px]">
            <h1 class="mb-6 text-2xl font-semibold">Shop</h1>

            <!-- Filter -->
            <form
                method="GET"
                :action="route('shop')"
                class="grid gap-3 sm:grid-cols-3"
            >
                <input
                    type="text"
                    name="q"
                    :value="filters.q"
                    class="w-full rounded-md border px-3 py-2 font-body text-sm"
                    placeholder="Cari produk…"
                />
                <select
                    name="root_id"
                    v-model="selectedRootId"
                    class="w-full rounded-md border px-3 py-2 font-body text-sm"
                >
                    <option :value="''">Semua Kategori Utama</option>
                    <option v-for="r in roots" :key="r.id" :value="r.id">
                        {{ r.name }}
                    </option>
                </select>
                <select
                    name="sub_id"
                    v-model="selectedSubId"
                    class="w-full rounded-md border px-3 py-2 font-body text-sm"
                >
                    <option :value="''">Semua Subkategori</option>
                    <option v-for="s in subsList" :key="s.id" :value="s.id">
                        {{ s.name }}
                    </option>
                </select>
                <div class="sm:col-span-3">
                    <button
                        class="mt-1 rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 font-body text-sm"
                    >
                        Terapkan Filter
                    </button>
                    <Link
                        :href="route('shop')"
                        class="ml-3 text-sm text-gray-600 hover:text-gray-900 font-body text-sm"
                        >Reset</Link
                    >
                </div>
            </form>

            <!-- Grid Produk -->
            <div
                class="grid-cols-2 mt-6 grid gap-6 sm:grid-cols-3 lg:grid-cols-4 "
            >
                <div
                    v-for="p in products"
                    :key="p.id"
                    class="relative overflow-hidden rounded-xl border bg-white transition hover:shadow-md"
                >
                    <!-- stretched link -->
                    <Link
                        :href="showHref(p)"
                        aria-label="Lihat detail produk"
                        class="absolute inset-0 z-10"
                    />

                    <!-- Gambar -->
                    <div class="bg-gray-50 p-6">
                        <img
                            :src="imgOf(p)"
                            :alt="p.name"
                            class="aspect-square w-full object-contain"
                        />
                    </div>

                    <!-- Body -->
                    <div class="p-4">
                        <Link
                            :href="showHref(p)"
                            class="relative z-20 line-clamp-2 font-medium hover:underline"
                            >{{ p.name }}</Link
                        >
                        <p class="mt-1 text-sm text-gray-600">
                            {{ categoryPath(p) }}
                        </p>
                        <p class="mt-1 font-semibold">
                            Rp {{ (p.price || 0).toLocaleString('id-ID') }}
                        </p>
                    </div>

                    <!-- BADGES -->
                    <div
                        class="pointer-events-none absolute left-3 top-3 z-20 space-y-1"
                    >
                        <span
                            v-if="isNew(p)"
                            class="rounded bg-indigo-600 px-2 py-0.5 text-[10px] font-medium text-white"
                            >NEW ARRIVAL</span
                        >
                    </div>
                </div>

                <p
                    v-if="!products.length"
                    class="col-span-full py-10 text-center text-gray-500"
                >
                    Tidak ada produk untuk filter ini.
                </p>
            </div>

            <!-- Pagination -->
            <nav v-if="links?.length" class="mt-8 flex flex-wrap gap-2 font-body text-sm">
                <Link
                    v-for="(l, i) in links"
                    :key="i"
                    :href="l.url || ''"
                    :class="[
                        'rounded border px-3 py-1.5',
                        l.active
                            ? 'border-indigo-600 bg-indigo-600 text-white'
                            : 'bg-white hover:bg-gray-50',
                    ]"
                    v-html="l.label"
                />
            </nav>
        </section>
    </SiteLayout>
</template>
