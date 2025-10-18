<script setup>
import { Head, Link, usePage, router } from '@inertiajs/vue3';
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

// dropdown state
const openRoot = ref(false);
const openSub = ref(false);

// klik di luar = tutup dropdown
document.addEventListener('click', () => {
  openRoot.value = false;
  openSub.value = false;
});
</script>

<template>
  <SiteLayout>
    <Head title="Shop" />

    <section class="mx-auto max-w-7xl px-4 pt-[100px] pb-10">
      <h1 class="mb-6 text-center text-2xl font-semibold text-brown dark:text-cream">Shop</h1>

      <!-- Filter -->
      <form
        method="GET"
        :action="route('shop')"
        class="grid gap-3 sm:grid-cols-3"
      >

        <!-- ROOT CATEGORY DROPDOWN -->
        <div class="relative">
          <input type="hidden" name="root_id" :value="selectedRootId" />
          <button
            type="button"
            @click.stop="openRoot = !openRoot"
            class="flex w-full items-center justify-between rounded-lg border border-gray-300 
                   bg-white/70 px-3 py-2 text-sm text-gray-800 shadow-sm backdrop-blur-md 
                   hover:bg-white/90 dark:bg-gray-900/60 dark:text-gray-100 
                   dark:border-gray-700 dark:hover:bg-gray-900/80"
          >
            {{
              selectedRootId
                ? roots.find((r) => String(r.id) === String(selectedRootId))?.name
                : 'Semua Kategori Utama'
            }}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-gray-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="2"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown list -->
          <transition name="fade">
            <div
              v-if="openRoot"
              class="absolute left-0 mt-1 w-full rounded-lg border border-gray-200 
                     bg-white/95 shadow-lg dark:border-gray-700 dark:bg-gray-900/95 z-50"
            >
              <div
                class="cursor-pointer px-3 py-2 text-sm hover:bg-sage/20 dark:hover:bg-gray-700/40"
                @click="selectedRootId = ''; openRoot = false"
              >
                Semua Kategori Utama
              </div>
              <div
                v-for="r in roots"
                :key="r.id"
                @click="selectedRootId = r.id; openRoot = false"
                class="cursor-pointer px-3 py-2 text-sm hover:bg-sage/20 dark:hover:bg-gray-700/40"
              >
                {{ r.name }}
              </div>
            </div>
          </transition>
        </div>

        <!-- SUB CATEGORY DROPDOWN -->
        <div class="relative">
          <input type="hidden" name="sub_id" :value="selectedSubId" />
          <button
            type="button"
            @click.stop="openSub = !openSub"
            class="flex w-full items-center justify-between rounded-lg border border-gray-300 
                   bg-white/70 px-3 py-2 text-sm text-gray-800 shadow-sm backdrop-blur-md 
                   hover:bg-white/90 dark:bg-gray-900/60 dark:text-gray-100 
                   dark:border-gray-700 dark:hover:bg-gray-900/80"
          >
            {{
              selectedSubId
                ? subsList.find((s) => String(s.id) === String(selectedSubId))?.name
                : 'Semua Subkategori'
            }}
            <svg
              xmlns="http://www.w3.org/2000/svg"
              class="h-4 w-4 text-gray-500"
              fill="none"
              viewBox="0 0 24 24"
              stroke-width="2"
              stroke="currentColor"
            >
              <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <!-- Dropdown list -->
          <transition name="fade">
            <div
              v-if="openSub"
              class="absolute left-0 mt-1 w-full rounded-lg border border-gray-200 
                     bg-white/95 shadow-lg dark:border-gray-700 dark:bg-gray-900/95 z-50"
            >
              <div
                class="cursor-pointer px-3 py-2 text-sm hover:bg-sage/20 dark:hover:bg-gray-700/40"
                @click="selectedSubId = ''; openSub = false"
              >
                Semua Subkategori
              </div>
              <div
                v-for="s in subsList"
                :key="s.id"
                @click="selectedSubId = s.id; openSub = false"
                class="cursor-pointer px-3 py-2 text-sm hover:bg-sage/20 dark:hover:bg-gray-700/40"
              >
                {{ s.name }}
              </div>
            </div>
          </transition>
        </div>

        <div class="flex items-center gap-3">
            <button
            class="rounded-md bg-sage dark:bg-brown px-4 py-2 text-white hover:bg-sage/40 dark:hover:bg-brown/40 font-body text-sm"
            >
            Terapkan Filter
            </button>
            <Link
            :href="route('shop')"
            class="text-sm text-gray-600 dark:text-offwhite hover:text-gray-900 dark:hover:text-cream font-body text-sm"
            >
            Reset
            </Link>
        </div>
      </form>

      <!-- Produk Grid dan Pagination tetap sama -->
      <div
        class="grid-cols-2 mt-6 grid gap-6 sm:grid-cols-3 lg:grid-cols-4 "
      >
        <div
          v-for="p in products"
          :key="p.id"
          class="relative overflow-hidden rounded-xl border border-sage/40 bg-cream/30 dark:bg-darkbrown/40 shadow-sm hover:shadow-md transition"
        >
          <Link
            :href="showHref(p)"
            aria-label="Lihat detail produk"
            class="absolute inset-0 z-10"
          />
          <div class="bg-gray-50 p-6">
            <img
              :src="imgOf(p)"
              :alt="p.name"
              class="aspect-square w-full object-contain bg-offwhite/70 dark:bg-gray-800"
            />
          </div>
          <div class="p-4">
            <Link
              :href="showHref(p)"
              class="relative z-20 line-clamp-2 font-medium hover:underline"
              >{{ p.name }}</Link
            >
            <p class="mt-1 text-sm text-gray-600 dark:text-offwhite">
              {{ categoryPath(p) }}
            </p>
            <p class="mt-1 font-semibold">
              Rp {{ (p.price || 0).toLocaleString('id-ID') }}
            </p>
          </div>
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
      <nav
        v-if="links?.length"
        class="mt-8 flex flex-wrap gap-2 font-body text-sm text-brown dark:text-cream "
      >
        <Link
          v-for="(l, i) in links"
          :key="i"
          :href="l.url || ''"
          :class="[
            'rounded border px-3 py-1.5',
            l.active
              ? 'bordersage bg-sage text-white'
              : 'bg-white dark:bg-darkbrown/40 hover:bg-gray-50',
          ]"
          v-html="l.label"
        />
      </nav>
    </section>
  </SiteLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>
