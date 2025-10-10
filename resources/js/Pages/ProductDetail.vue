<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

const page = usePage();
const product = page.props.product || {};
const related = page.props.related || [];

// helper
const imgUrl = (img, fallbackId = product?.id) => {
    const idx = Number(fallbackId) % 8 || 1;
    return img?.url || `/theme/images/p${idx}.png`;
};
const categoryPath = (p) => {
    const cat = p?.category;
    if (!cat) return '';
    return cat?.parent?.name ? `${cat.parent.name} → ${cat.name}` : cat.name;
};

// kumpulan gambar (kalau kosong, siapkan 1 dummy agar aman)
const images = computed(() => {
    const arr = Array.isArray(product.images) ? product.images : [];
    return arr.length
        ? arr
        : [{ url: `/theme/images/p${Number(product?.id) % 8 || 1}.png` }];
});

// index gambar aktif
const selected = ref(0);

// url gambar utama
const mainUrl = computed(() =>
    imgUrl(images.value[selected.value], product?.id),
);

function select(i) {
    if (i >= 0 && i < images.value.length) selected.value = i;
}
function prev() {
    selected.value =
        (selected.value - 1 + images.value.length) % images.value.length;
}
function next() {
    selected.value = (selected.value + 1) % images.value.length;
}
</script>

<template>
    <SiteLayout>
        <Head :title="product.name || 'Product Detail'" />

        <section class="mx-auto max-w-7xl px-4 py-8">
            <!-- Breadcrumbs sederhana -->
            <nav class="text-sm text-gray-600">
                <Link :href="route('landing')" class="hover:underline"
                    >Home</Link
                >
                <span class="mx-2">/</span>
                <Link :href="route('shop')" class="hover:underline">Shop</Link>
                <template v-if="product?.category">
                    <span class="mx-2">/</span>
                    <span>{{ categoryPath(product) }}</span>
                </template>
            </nav>

            <div class="mt-6 grid gap-8 lg:grid-cols-2">
                <!-- Gallery -->
                <div>
                    <div
                        class="relative overflow-hidden rounded-xl border bg-gray-50"
                    >
                        <img
                            :src="mainUrl"
                            :alt="product.name"
                            class="aspect-square w-full object-contain p-6"
                        />

                        <!-- Nav tombol kiri/kanan -->
                        <button
                            v-if="images.length > 1"
                            type="button"
                            @click="prev"
                            class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border bg-white/80 px-3 py-2 shadow hover:bg-white"
                            aria-label="Previous image"
                        >
                            ‹
                        </button>

                        <button
                            v-if="images.length > 1"
                            type="button"
                            @click="next"
                            class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border bg-white/80 px-3 py-2 shadow hover:bg-white"
                            aria-label="Next image"
                        >
                            ›
                        </button>
                    </div>

                    <!-- Thumbnails -->
                    <div v-if="images.length" class="mt-3 flex flex-wrap gap-3">
                        <button
                            v-for="(img, i) in images"
                            :key="i"
                            type="button"
                            @click="select(i)"
                            :class="[
                                'h-20 w-20 overflow-hidden rounded border bg-white focus:outline-none',
                                i === selected
                                    ? 'border-indigo-600 ring-2 ring-indigo-600'
                                    : 'hover:border-gray-400',
                            ]"
                            title="Preview image"
                        >
                            <img
                                :src="imgUrl(img)"
                                class="h-full w-full object-cover"
                            />
                        </button>
                    </div>
                </div>

                <!-- Info -->
                <div>
                    <h1 class="text-2xl font-semibold">{{ product.name }}</h1>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ categoryPath(product) }}
                    </p>
                    <p class="mt-3 text-2xl font-bold">
                        Rp {{ (product.price || 0).toLocaleString('id-ID') }}
                    </p>

                    <div class="prose prose-sm mt-6 max-w-none">
                        <h3 class="mb-1">Description</h3>
                        <p
                            v-if="product.description"
                            v-html="product.description"
                        ></p>
                        <p v-else class="text-gray-500">No description.</p>
                    </div>

                    <div class="mt-6 flex gap-3">
                        <button class="btn-primary">Add to Cart</button>
                        <Link
                            :href="route('shop')"
                            class="inline-flex items-center rounded-md border px-4 py-2"
                        >
                            Back to Shop
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Related products -->
            <section v-if="related.length" class="mt-12">
                <h2 class="text-lg font-semibold">Related Products</h2>
                <div
                    class="xs:grid-cols-2 mt-4 grid gap-6 sm:grid-cols-3 lg:grid-cols-4"
                >
                    <article
                        v-for="rp in related"
                        :key="rp.id"
                        class="overflow-hidden rounded-xl border bg-white"
                    >
                        <Link :href="route('product.show', rp.slug ?? rp.id)">
                            <div class="bg-gray-50">
                                <img
                                    :src="imgUrl(rp.images?.[0], rp.id)"
                                    :alt="rp.name"
                                    class="aspect-square w-full object-contain p-6"
                                />
                            </div>
                        </Link>
                        <div class="p-4">
                            <Link
                                :href="route('product.show', rp.slug ?? rp.id)"
                                class="font-medium hover:underline"
                            >
                                {{ rp.name }}
                            </Link>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ categoryPath(rp) }}
                            </p>
                            <p class="mt-1 font-semibold">
                                Rp {{ (rp.price || 0).toLocaleString('id-ID') }}
                            </p>
                        </div>
                    </article>
                </div>
            </section>
        </section>
    </SiteLayout>
</template>
