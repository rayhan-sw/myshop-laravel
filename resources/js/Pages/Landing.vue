<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { ref, onMounted, onBeforeUnmount } from 'vue';

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

// Slider
const slides = [
  { type: 'image', src: '/theme/images/hero1.jpg', title: 'Welcome To Our Gift Shop' },
  { type: 'video', src: '/theme/videos/hero-video.mp4', title: 'Second Soul' },
];

const currentSlide = ref(0);
let timer = null;

// Auto slide setiap 5 detik
onMounted(() => {
  timer = setInterval(() => {
    currentSlide.value = (currentSlide.value + 1) % slides.length;
  }, 5000);
});

onBeforeUnmount(() => {
  clearInterval(timer);
});

// Navigasi manual
const nextSlide = () => {
  currentSlide.value = (currentSlide.value + 1) % slides.length;
};
const prevSlide = () => {
  currentSlide.value = (currentSlide.value - 1 + slides.length) % slides.length;
};


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

        <!-- HERO SLIDER SECTION -->
        <section class="relative overflow-hidden bg-sage dark:bg-brown">

        <!-- Slide 1: IMAGE HERO -->
        <div
        v-if="currentSlide === 0"
        class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-12 pt-[100px] min-h-[calc(100vh-72px)] lg:grid-cols-2"
        >
        <!-- Text -->
        <div class="space-y-6 text-white text-center lg:text-left">
            <h1 class="text-brown dark:text-sage font-extrabold text-4xl md:text-5xl leading-tight">
            Welcome To Our <br />
            <span class="text-offwhite">Gift Shop</span>
            </h1>
            <p class="max-w-prose text-sm md:text-base text-brown dark:text-offwhite font-body mx-auto lg:mx-0">
            “Second Soul” berangkat dari gagasan bahwa setiap barang memiliki cerita dan kenangan dari pemilik sebelumnya. Saat berpindah tangan, barang itu tidak kehilangan nilainya - justru mendapatkan jiwa baru melalui makna yang diberikan oleh pemilik berikutnya."
            </p>
            <div class="flex flex-wrap justify-center lg:justify-start gap-3">
            <Link :href="route('contact')" class="inline-flex items-center justify-center font-body rounded-md border border-sage bg-brown dark:bg-sage text-sage dark:text-brown px-4 py-2 hover:bg-offwhite hover:border-brown transition"> Contact Us </Link> 
            <Link :href="route('shop')" class="inline-flex items-center justify-center font-body rounded-md border border-brown dark:border-sage px-4 py-2 text-brown dark:text-sage hover:bg-white/10"> Shop Now </Link>
            </div>
        </div>

        <!-- Image -->
        <div class="relative">
            <img src="/theme/images/image3.jpeg" alt="Hero" class="w-full rounded-lg object-cover shadow max-h-[400px] mx-auto" />
        </div>
        </div>

        <!-- Slide 2: VIDEO HERO -->
        <div v-else class="relative w-full min-h-[calc(100vh-70px)] pt-[80px] ">
        <video
            autoplay
            muted
            loop
            playsinline
            class="absolute inset-0 w-full h-full object-cover z-0"
        >
            <source src="/theme/videos/dashvid.mp4" type="video/mp4" />
            Browser Anda tidak mendukung video.
        </video>

        <!-- Overlay content -->
        <div class="absolute inset-0 bg-black/40 flex items-center justify-center z-10 px-4 text-center">
            <div class="text-white max-w-2xl">
            <h1 class="text-3xl md:text-5xl font-extrabold text-white">{{ slides[1].title }}</h1>
            <p class="mt-4 text-sm md:text-base text-gray-200">
                Setiap produk punya cerita...
            </p>
            <div class="mt-6 flex justify-center gap-3 flex-wrap">
                <Link :href="route('contact')" class="inline-flex items-center justify-center font-body rounded-md border border-sage bg-brown text-sage px-4 py-2 hover:bg-offwhite hover:border-brown transition"> Contact Us </Link> 
            <Link :href="route('shop')" class="inline-flex items-center justify-center font-body rounded-md border border-brown px-4 py-2 text-brown hover:bg-white/10"> Shop Now </Link>
            </div>
            </div>
        </div>
        </div>


        <!-- Manual Controls -->
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-3 z-20">
            <button @click="prevSlide" class="w-10 h-10 rounded-full bg-white/70 hover:bg-white text-gray-800 font-bold">&lt;</button>
            <button @click="nextSlide" class="w-10 h-10 rounded-full bg-white/70 hover:bg-white text-gray-800 font-bold">&gt;</button>
        </div>

        </section>

        <!-- Produk terbaru -->
        <section v-if="products.length" class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="text-center text-xl font-semibold">Produk</h2>

            <div
                class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-6 mt-8"
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

                        
                    </div>

                    <!-- BADGES -->
                    <div
                        class="pointer-events-none absolute left-3 top-3 z-20 space-y-1"
                    >
                        <!-- NEW badge: warna sama dengan tombol -->
                        <span
                            v-if="isNew(p)"
                            class="rounded bg-sage px-2 py-0.5 text-[10px] font-medium text-offwhite"
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
