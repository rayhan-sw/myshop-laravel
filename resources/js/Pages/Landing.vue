<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// Fitur “Why choose…”
const features = [
    {
        title: 'Fast Delivery',
        desc: 'Pengiriman cepat dan aman.',
        icon: '/theme/images/truck.svg',
    },
    {
        title: 'Best Quality',
        desc: 'Kualitas produk unggulan.',
        icon: '/theme/images/high-quality.svg',
    },
    {
        title: 'Free Shipping',
        desc: 'Gratis ongkir untuk syarat tertentu.',
        icon: '/theme/images/free.svg',
    },
];

// Data dari server
const page = usePage();
const products = page.props.homeProducts ?? page.props.latestProducts ?? [];

// Helper tampilan
const categoryPath = (p) => {
    const cat = p?.category;
    if (!cat) return '';
    return cat?.parent?.name ? `${cat.parent.name} → ${cat.name}` : cat.name;
};
const imgOf = (p) => {
    const url = p?.images?.[0]?.url;
    if (url) return url;
    const idx = Number(p?.id) % 8 || 1;
    return `/theme/images/p${idx}.png`;
};

// === NEW badge helper (≤ 14 hari)
const isNew = (p, days = 14) => {
    const d = p?.created_at ? new Date(p.created_at) : null;
    if (!d || isNaN(d)) return false;
    const diffDays = (Date.now() - d.getTime()) / 86400000;
    return diffDays <= days;
};

// Navigasi ke detail produk
const showHref = (p) => route('product.show', p.slug ?? p.id);
const goDetail = (p) => router.visit(showHref(p));

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
        <Head title="Home" />

        <!-- Hero -->
        <section class="border-b bg-black">
            <div
                class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-12 lg:grid-cols-2"
            >
                <div class="space-y-6 text-white">
                    <h1 class="text-4xl/tight font-extrabold md:text-5xl/tight">
                        Welcome To Our <br /><span class="text-indigo-400"
                            >Gift Shop</span
                        >
                    </h1>
                    <p class="max-w-prose text-sm text-gray-300">
                        Sequi perspiciatis nulla reiciendis, rem, tenetur
                        impedit, eveniet non necessitatibus error distinctio
                        mollitia suscipit. Nostrum fugit.
                    </p>
                    <div class="flex gap-3">
                        <Link :href="route('contact')" class="btn-primary"
                            >Contact Us</Link
                        >
                        <Link
                            :href="route('shop')"
                            class="inline-flex items-center justify-center rounded-md border border-indigo-200 px-4 py-2 text-indigo-200 hover:bg-white/10"
                        >
                            Shop Now
                        </Link>
                    </div>
                </div>

                <div class="relative">
                    <img
                        src="/theme/images/image3.jpeg"
                        alt="Hero"
                        class="h-auto w-full rounded-lg object-cover shadow"
                    />
                    <img
                        src="/theme/images/slider-bg.jpg"
                        alt=""
                        class="pointer-events-none absolute -left-8 -top-8 -z-10 w-40 opacity-20"
                    />
                </div>
            </div>
        </section>

        <!-- Produk terbaru -->
        <section v-if="products.length" class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="text-center text-xl font-semibold">Produk</h2>

            <div
                class="xs:grid-cols-2 mt-8 grid gap-6 sm:grid-cols-3 lg:grid-cols-4"
            >
                <div
                    v-for="p in products"
                    :key="p.id"
                    class="group relative overflow-hidden rounded-xl border bg-white transition hover:shadow-md"
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
                    <div class="space-y-1 p-4">
                        <Link
                            :href="showHref(p)"
                            class="relative z-20 font-medium text-indigo-700 hover:underline"
                            >{{ p.name }}</Link
                        >
                        <p class="text-sm text-gray-500">
                            {{ categoryPath(p) }}
                        </p>
                        <p class="font-semibold">
                            Rp {{ (p.price || 0).toLocaleString('id-ID') }}
                        </p>

                        <button
                            type="button"
                            class="relative z-20 mt-3 w-full rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white transition hover:bg-indigo-700"
                            @click.stop="addToCart(p.id)"
                        >
                            Add to cart
                        </button>
                    </div>

                    <!-- BADGES -->
                    <div
                        class="pointer-events-none absolute left-3 top-3 z-20 space-y-1"
                    >
                        <!-- NEW badge: warna sama dengan tombol -->
                        <span
                            v-if="isNew(p)"
                            class="rounded bg-indigo-600 px-2 py-0.5 text-[10px] font-medium text-white"
                        >
                            NEW ARRIVAL
                        </span>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <Link :href="route('shop')" class="btn-primary inline-flex"
                    >View all Products</Link
                >
            </div>
        </section>

        <!-- Why choose -->
        <section class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="text-center text-2xl font-semibold">
                WHY CHOOSE MY SHOP
            </h2>
            <p class="mt-2 text-center text-gray-500">
                Great products, fair prices, and fast delivery.
            </p>
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(f, i) in features"
                    :key="i"
                    class="rounded-xl border p-6 text-center"
                >
                    <img
                        :src="f.icon"
                        :alt="f.title"
                        class="mx-auto h-16 w-16 object-contain"
                    />
                    <h3 class="mt-4 font-semibold">{{ f.title }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ f.desc }}</p>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
