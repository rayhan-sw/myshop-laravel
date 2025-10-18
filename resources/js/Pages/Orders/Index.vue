<script setup>
import { Head, Link } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';

const props = defineProps({
    orders: { type: Array, default: () => [] },
});

function money(n) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
    }).format(Number(n || 0));
}

function badgeClass(status) {
    switch (status) {
        case 'pending':
            return 'bg-yellow-50 text-yellow-700';
        case 'diproses':
            return 'bg-blue-50 text-blue-700';
        case 'dikirim':
            return 'bg-indigo-50 text-indigo-700';
        case 'selesai':
            return 'bg-emerald-50 text-emerald-700';
        case 'batal':
            return 'bg-rose-50 text-rose-700';
        default:
            return 'bg-gray-100 text-gray-600';
    }
}
</script>

<template>
    <SiteLayout>
        <Head title="Pesanan Saya" />
        <section class="mx-auto max-w-5xl px-4 py-8 pt-[92px]">
            <h1 class="mb-4 text-2xl font-semibold text-brown dark:text-sage">Pesanan Saya</h1>

            <div
                v-if="!orders.length"
                class="rounded-xl border bg-white p-6 text-center text-gray-500"
            >
                Belum ada pesanan.
                <Link
                    :href="route('shop')"
                    class="text-indigo-600 hover:underline"
                >
                    Belanja dulu </Link
                >.
            </div>

            <div v-else class="space-y-4 text-brown dark:text-cream">
                <!-- ✅ pakai nomor lokal per user -->
                <div
                    v-for="(o, i) in orders"
                    :key="o.id"
                    class="rounded-xl border dark:border-sage bg-cream/30 dark:bg-darkbrown/40 p-5"
                >
                    <div
                        class="flex flex-wrap items-center justify-between gap-3"
                    >
                        <div>
                            <p class="font-semibold">Order #{{ i + 1 }}</p>
                            <p class="text-sm text-gray-500 dark:text-offwhite">
                                Tanggal:
                                {{
                                    new Date(o.created_at).toLocaleString(
                                        'id-ID',
                                    )
                                }}
                            </p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span
                                class="rounded-full px-3 py-1 text-sm font-medium"
                                :class="badgeClass(o.status)"
                            >
                                {{ o.status }}
                            </span>
                            <span class="text-lg font-bold">{{
                                money(o.total)
                            }}</span>
                        </div>
                    </div>

                    <div class="mt-4 divide-y">
                        <div
                            v-for="it in o.items"
                            :key="it.id"
                            class="flex items-center justify-between py-2"
                        >
                            <div>
                                <p class="font-medium">
                                    {{ it.product?.name }}
                                </p>
                                <p class="text-sm text-gray-600 dark:text-offwhite">
                                    x{{ it.qty }} · {{ money(it.price) }}
                                </p>
                            </div>
                            <p class="font-medium">{{ money(it.subtotal) }}</p>
                        </div>
                    </div>

                    <div class="mt-3 text-sm text-gray-600 dark:text-offwhite">
                        <p>
                            <span class="font-medium">Alamat:</span>
                            {{ o.address_text }}
                        </p>
                    </div>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
