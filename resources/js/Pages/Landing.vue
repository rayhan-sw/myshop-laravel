<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// ====== Bagian lama: fitur/why choose (dipertahankan) ======
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

// ====== Data dari server (tanpa dummy) ======
const page = usePage();
// Gunakan salah satu: homeProducts (kalau kamu kirim) atau latestProducts (yang sudah ada)
const products = page.props.homeProducts ?? page.props.latestProducts ?? [];

// helper tampilan
const categoryPath = (p) => {
    const cat = p?.category;
    if (!cat) return '';
    return cat?.parent?.name ? `${cat.parent.name} → ${cat.name}` : cat.name;
};

// ambil gambar upload (url accessor) atau fallback dummy
const imgOf = (p) => {
    const url = p?.images?.[0]?.url;
    if (url) return url;
    const idx = Number(p?.id) % 8 || 1;
    return `/theme/images/p${idx}.png`;
};

// link detail: pakai slug kalau ada, fallback ke id
const showHref = (p) => route('product.show', p.slug ?? p.id);
</script>

<template>
    <SiteLayout>
        <Head title="Home" />

        <!-- ====== Hero (dipertahankan) ====== -->
        <section class="border-b bg-black">
            <div
                class="mx-auto grid max-w-7xl items-center gap-8 px-4 py-12 lg:grid-cols-2"
            >
                <div class="space-y-6 text-white">
                    <h1 class="text-4xl/tight font-extrabold md:text-5xl/tight">
                        Welcome To Our <br />
                        <span class="text-indigo-400">Gift Shop</span>
                    </h1>
                    <p class="max-w-prose text-sm text-gray-300">
                        Sequi perspiciatis nulla reiciendis, rem, tenetur
                        impedit, eveniet non necessitatibus error distinctio
                        mollitia suscipit. Nostrum fugit doloribus consequuntur
                        distinctio esse.
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

        <!-- ====== Produk (gabungan dari admin) — kartu bisa diklik ke detail ====== -->
        <section v-if="products.length" class="mx-auto max-w-7xl px-4 py-12">
            <h2 class="text-center text-xl font-semibold">Produk</h2>
            <div
                class="xs:grid-cols-2 mt-8 grid gap-6 sm:grid-cols-3 lg:grid-cols-4"
            >
                <article
                    v-for="p in products"
                    :key="p.id"
                    class="overflow-hidden rounded-xl border bg-white"
                >
                    <Link :href="showHref(p)">
                        <div class="bg-gray-50">
                            <img
                                :src="imgOf(p)"
                                :alt="p.name"
                                class="aspect-square w-full object-contain p-6"
                            />
                        </div>
                    </Link>
                    <div class="p-4">
                        <Link
                            :href="showHref(p)"
                            class="font-medium hover:underline"
                        >
                            {{ p.name }}
                        </Link>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ categoryPath(p) }}
                        </p>
                        <p class="mt-1 font-semibold">
                            Rp {{ (p.price || 0).toLocaleString('id-ID') }}
                        </p>
                    </div>
                </article>
            </div>
            <div class="mt-8 text-center">
                <Link :href="route('shop')" class="btn-primary inline-flex"
                    >View all Products</Link
                >
            </div>
        </section>

        <!-- ====== Why choose (dipertahankan) ====== -->
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

        <!-- ====== Contact + Map (dipertahankan) ====== -->
        <section class="mx-auto max-w-7xl px-4 pb-16">
            <div class="grid gap-8 lg:grid-cols-2">
                <div class="overflow-hidden rounded-lg border">
                    <iframe
                        src="https://maps.google.com/maps?q=jakarta&t=&z=12&ie=UTF8&iwloc=&output=embed"
                        width="100%"
                        height="420"
                        style="border: 0"
                        loading="lazy"
                        allowfullscreen
                    />
                </div>
                <form @submit.prevent class="space-y-3">
                    <h3 class="text-xl font-semibold">CONTACT US</h3>
                    <input
                        type="text"
                        placeholder="Name"
                        class="w-full rounded-md border px-3 py-2"
                    />
                    <input
                        type="email"
                        placeholder="Email"
                        class="w-full rounded-md border px-3 py-2"
                    />
                    <input
                        type="text"
                        placeholder="Phone"
                        class="w-full rounded-md border px-3 py-2"
                    />
                    <textarea
                        rows="5"
                        placeholder="Message"
                        class="w-full rounded-md border px-3 py-2"
                    />
                    <button class="btn-primary">Send</button>
                </form>
            </div>
        </section>
    </SiteLayout>
</template>
