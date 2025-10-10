<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// Props dari backend (ShopController@shop)
const page = usePage();
const products = page.props.products?.data ?? [];
const links = page.props.products?.links ?? [];
const roots = page.props.roots ?? [];
const filters = page.props.filters ?? { q: '', root_id: '', sub_id: '' };

// STATE pilihan filter (pakai v-model supaya dropdown reaktif)
const selectedRootId = ref(filters.root_id ?? '');
const selectedSubId = ref(filters.sub_id ?? '');

// reset sub jika root diganti
watch(selectedRootId, () => {
    selectedSubId.value = '';
});

// util: anak dari root tertentu
const subsOf = (rootId) =>
    roots.find((r) => String(r.id) === String(rootId))?.children ?? [];

// semua subkategori dari semua root
const allSubs = computed(() => roots.flatMap((r) => r.children ?? []));

// sumber options subkategori: jika root dipilih => sub dari root tsb; kalau tidak => semua sub
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

// link detail: pakai slug kalau ada, fallback ke id
const showHref = (p) => route('product.show', p.slug ?? p.id);
</script>

<template>
    <SiteLayout>
        <Head title="Shop" />

        <section class="mx-auto max-w-7xl px-4 py-10">
            <div class="flex items-end justify-between gap-4">
                <h1 class="text-2xl font-semibold">Shop</h1>
            </div>

            <!-- Filter -->
            <form
                method="GET"
                :action="route('shop')"
                class="mt-6 grid gap-3 sm:grid-cols-3"
            >
                <!-- Search -->
                <input
                    type="text"
                    name="q"
                    :value="filters.q"
                    class="w-full rounded-md border px-3 py-2"
                    placeholder="Cari produk…"
                />

                <!-- Root category -->
                <select
                    name="root_id"
                    v-model="selectedRootId"
                    class="w-full rounded-md border px-3 py-2"
                >
                    <option :value="''">Semua Kategori Utama</option>
                    <option v-for="r in roots" :key="r.id" :value="r.id">
                        {{ r.name }}
                    </option>
                </select>

                <!-- Subcategory: jika root kosong => tampilkan SEMUA subkategori -->
                <select
                    name="sub_id"
                    v-model="selectedSubId"
                    class="w-full rounded-md border px-3 py-2"
                >
                    <option :value="''">Semua Subkategori</option>
                    <option v-for="s in subsList" :key="s.id" :value="s.id">
                        {{ s.name }}
                    </option>
                </select>

                <div class="sm:col-span-3">
                    <button
                        class="mt-1 rounded-md bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700"
                    >
                        Terapkan Filter
                    </button>
                    <Link
                        :href="route('shop')"
                        class="ml-3 text-sm text-gray-600 hover:text-gray-900"
                    >
                        Reset
                    </Link>
                </div>
            </form>

            <!-- (Opsional) Deretan quick-pills subkategori saat root kosong -->
            <div
                v-if="!selectedRootId && subsList.length"
                class="mx-auto mt-4 flex flex-wrap gap-2"
            >
                <Link
                    v-for="s in subsList"
                    :key="s.id"
                    :href="route('shop', { sub_id: s.id })"
                    class="rounded-full border px-3 py-1 text-sm hover:bg-gray-50"
                >
                    {{ s.name }}
                </Link>
            </div>

            <!-- Grid Produk dari admin (kartu bisa diklik ke detail) -->
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
                            class="line-clamp-2 font-medium hover:underline"
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

                <p
                    v-if="!products.length"
                    class="col-span-full py-10 text-center text-gray-500"
                >
                    Tidak ada produk untuk filter ini.
                </p>
            </div>

            <!-- Pagination -->
            <nav v-if="links?.length" class="mt-8 flex flex-wrap gap-2">
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
