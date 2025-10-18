<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// Props
const props = defineProps({
    product: { type: Object, default: null },
    related: { type: Array, default: () => [] },
});

const product = props.product || {};
const related = props.related || [];

/* Helpers */
const money = (n) =>
    new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(Number(n || 0));

const imgUrl = (img, fallbackId = product?.id) =>
    img?.url || `/theme/images/p${Number(fallbackId) % 8 || 1}.png`;

const categoryPath = (p) => {
    const c = p?.category;
    if (!c) return '';
    return c?.parent?.name ? `${c.parent.name} → ${c.name}` : c.name;
};

/* Detail gallery */
const images = computed(() => {
    const arr = Array.isArray(product.images) ? product.images : [];
    return arr.length
        ? arr
        : [{ url: `/theme/images/p${Number(product?.id) % 8 || 1}.png` }];
});
const selected = ref(0);
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

/* Stok & qty */
const maxStock = computed(() => Number(product?.stock ?? 0)); // ganti ke product?.quantity kalau kolomnya 'quantity'
const inStock = computed(() => maxStock.value > 0);
const qty = ref(inStock.value ? 1 : 0);
function clampQty() {
    if (!inStock.value) {
        qty.value = 0;
        return;
    }
    qty.value = Math.max(1, Math.min(qty.value || 1, maxStock.value));
}
function dec() {
    if (inStock.value) qty.value = Math.max(1, (qty.value || 1) - 1);
}
function inc() {
    if (inStock.value)
        qty.value = Math.min(maxStock.value, (qty.value || 1) + 1);
}

/* Add to Cart / Buy Now */
async function addToCartInternal(goCartAfter = false) {
    if (!inStock.value) {
        window.Swal?.fire({
            icon: 'warning',
            title: 'Stok habis',
            text: 'Maaf, stok produk ini kosong.',
        });
        return;
    }
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
        body: JSON.stringify({ product_id: product.id, qty: qty.value }),
    });

    if (res.ok) {
        if (goCartAfter) router.visit(route('cart.index'));
        else
            window.Swal?.fire({
                toast: true,
                position: 'top-end',
                icon: 'success',
                title: 'Ditambahkan ke cart',
                timer: 1400,
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
function addToCart() {
    addToCartInternal(false);
}
function buyNow() {
    addToCartInternal(true);
}

/* Related: klik kartu */
const showHref = (p) => route('product.show', p.slug ?? p.id);
const goDetail = (p) => router.visit(showHref(p));

/* === ZOOM STATE === */
const zoomOpen = ref(false);
const zoomScale = ref(1);
const zoomPos = ref({ x: 0, y: 0 });
let dragging = false;
let dragStart = { x: 0, y: 0 };
let posStart = { x: 0, y: 0 };

function openZoom() {
    zoomOpen.value = true;
    zoomScale.value = 1;
    zoomPos.value = { x: 0, y: 0 };
}
function closeZoom() {
    zoomOpen.value = false;
}
function onWheel(e) {
    e.preventDefault();
    const next = Math.min(
        4,
        Math.max(1, zoomScale.value + (e.deltaY < 0 ? 0.1 : -0.1)),
    );
    zoomScale.value = Number(next.toFixed(2));
}
function startDrag(e) {
    dragging = true;
    const p = e.touches?.[0] ?? e;
    dragStart = { x: p.clientX, y: p.clientY };
    posStart = { ...zoomPos.value };
}
function onDrag(e) {
    if (!dragging) return;
    const p = e.touches?.[0] ?? e;
    zoomPos.value = {
        x: posStart.x + (p.clientX - dragStart.x),
        y: posStart.y + (p.clientY - dragStart.y),
    };
}
function endDrag() {
    dragging = false;
}
onMounted(() => {
    window.addEventListener('mouseup', endDrag);
    window.addEventListener('touchend', endDrag);
});
onBeforeUnmount(() => {
    window.removeEventListener('mouseup', endDrag);
    window.removeEventListener('touchend', endDrag);
});
</script>

<template>
    <SiteLayout>
        <!-- Guard supaya tidak blank -->
        <div v-if="product && product.id">
            <Head :title="product.name || 'Product Detail'" />

            <section class="mx-auto max-w-7xl p-6 pt-[100px]">
                <div class="grid gap-6 md:grid-cols-2">
                    <!-- Gallery -->
                    <div>
                        <img
                            :src="mainUrl"
                            alt="Product"
                            class="aspect-square w-full cursor-zoom-in rounded-lg border object-cover"
                            @click="openZoom"
                        />
                        <div class="mt-3 flex justify-center gap-2">
                            <img
                                v-for="(img, i) in images"
                                :key="i"
                                :src="img.url"
                                :alt="'thumb-' + i"
                                @click="select(i)"
                                class="h-16 w-16 cursor-pointer rounded border object-cover"
                                :class="{
                                    'ring-2 ring-indigo-500': selected === i,
                                }"
                            />
                        </div>

                        <div
                            v-if="images.length > 1"
                            class="mt-3 flex justify-between"
                        >
                            <button
                                class="rounded border px-3 py-1"
                                @click="prev"
                            >
                                ‹ Prev
                            </button>
                            <button
                                class="rounded border px-3 py-1"
                                @click="next"
                            >
                                Next ›
                            </button>
                        </div>
                    </div>

                    <!-- Info -->
                    <div>
                        <h1 class="text-2xl font-semibold dark:text-cream">
                            {{ product.name }}
                        </h1>
                        <p class="mt-1 text-gray-500">
                            {{ categoryPath(product) }}
                        </p>
                        <p class="mt-3 text-xl font-bold text-brown dark:text-cream">
                            {{ money(product.price) }}
                        </p>
                        <p class="mt-2 leading-relaxed text-brown dark:text-offwhite">
                            {{ product.description }}
                        </p>

                        <!-- Stok badge -->
                        <p class="mt-2 text-sm">
                            <span
                                v-if="inStock"
                                class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 font-medium text-green-700"
                            >
                                Stok: {{ maxStock }}
                            </span>
                            <span
                                v-else
                                class="inline-flex items-center rounded-full bg-rose-100 px-3 py-1 font-medium text-rose-700"
                            >
                                Stok habis
                            </span>
                        </p>

                        <div class="mt-4 flex items-center gap-3">
                            <div
                                class="inline-flex items-center rounded border"
                            >
                                <button
                                    class="px-3 py-2"
                                    @click="dec"
                                    :disabled="!inStock"
                                >
                                    -
                                </button>
                                <input
                                    class="w-14 border-x px-2 py-2 text-center bg-white dark:bg-brown focus:outline-none"
                                    type="number"
                                    v-model.number="qty"
                                    @input="clampQty"
                                    :min="inStock ? 1 : 0"
                                    :max="maxStock"
                                    :disabled="!inStock"
                                />
                                <button
                                    class="px-3 py-2"
                                    @click="inc"
                                    :disabled="!inStock"
                                >
                                    +
                                </button>
                            </div>

                            <button
                                class="rounded bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 disabled:opacity-50"
                                @click="addToCart"
                                :disabled="!inStock"
                            >
                                Add to Cart
                            </button>
                            <button
                                class="rounded bg-rose-600 px-4 py-2 text-white hover:bg-rose-700 disabled:opacity-50"
                                @click="buyNow"
                                :disabled="!inStock"
                            >
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Related Products -->
                <section v-if="related && related.length" class="mt-12">
                    <h2 class="text-lg font-semibold text-brown dark:text-cream">Related Products</h2>

                    <div
                        class="xs:grid-cols-2 mt-4 grid gap-6 sm:grid-cols-3 lg:grid-cols-4"
                    >
                        <div
                            v-for="rp in related"
                            :key="rp.id"
                            class="group relative overflow-hidden rounded-xl border border-sage/40 bg-cream/30 dark:bg-darkbrown/40 shadow-sm hover:shadow-md transition"
                            @click="goDetail(rp)"
                        >
                            <div class="bg-gray-50 p-6">
                                <img
                                    :src="imgUrl(rp.images?.[0], rp.id)"
                                    :alt="rp.name"
                                    class="aspect-square w-full object-contain bg-offwhite/70 dark:bg-gray-800"
                                />
                            </div>

                            <div class="p-4">
                                <Link
                                    :href="showHref(rp)"
                                    class="relative z-20 font-medium hover:underline"
                                >
                                    {{ rp.name }}
                                </Link>
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ categoryPath(rp) }}
                                </p>
                                <p class="mt-1 font-semibold">
                                    Rp
                                    {{
                                        (rp.price || 0).toLocaleString('id-ID')
                                    }}
                                </p>
                            </div>

                            <div
                                class="pointer-events-none absolute left-3 top-3 z-20"
                                v-if="rp.images?.length > 1"
                            >
                                <span
                                    class="rounded bg-black/70 px-2 py-0.5 text-[10px] font-medium text-white"
                                >
                                    {{ rp.images.length }} fotos
                                </span>
                            </div>
                        </div>
                    </div>
                </section>
            </section>
        </div>

        <!-- Fallback -->
        <div v-else class="p-10 text-center text-gray-500">
            Produk tidak ditemukan.
        </div>

        <!-- ZOOM MODAL -->
        <div
            v-if="zoomOpen"
            class="fixed inset-0 z-[999] flex items-center justify-center bg-black/80 p-4"
            @click.self="closeZoom"
        >
            <button
                class="absolute right-4 top-4 rounded bg-white/10 px-3 py-1 text-white hover:bg-white/20"
                @click="closeZoom"
            >
                ✕ Close
            </button>

            <img
                :src="mainUrl"
                alt="Zoomed"
                class="max-h-[90vh] max-w-[90vw] cursor-grab select-none active:cursor-grabbing"
                :style="{
                    transform: `translate(${zoomPos.x}px, ${zoomPos.y}px) scale(${zoomScale})`,
                    transition: dragging ? 'none' : 'transform 120ms ease-out',
                }"
                @wheel.passive="onWheel"
                @mousedown="startDrag"
                @mousemove="onDrag"
                @touchstart.passive="startDrag"
                @touchmove.passive="onDrag"
            />
        </div>
    </SiteLayout>
</template>
