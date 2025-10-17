<script setup>
import { Head } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
import { ref } from 'vue';

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
        desc: 'Gratis ongkir syarat & ketentuan.',
        icon: '/theme/images/free.svg',
    },
];

const faqs = [
    {
        q: 'Bagaimana cara melakukan pemesanan?',
        a: 'Pilih produk yang diinginkan, masukkan ke keranjang, lalu lakukan checkout.',
    },
    {
        q: 'Apakah bisa retur barang?',
        a: 'Ya, retur bisa dilakukan dalam 7 hari dengan syarat barang masih utuh.',
    },
    {
        q: 'Metode pembayaran apa saja yang tersedia?',
        a: 'Kami menerima transfer bank, e-wallet, dan kartu kredit.',
    },
];

const openIndex = ref(null);

function toggle(i) {
    openIndex.value = openIndex.value === i ? null : i;
}
</script>

<template>
    <SiteLayout>
        <Head title="Why Us" />

        <!-- WHY CHOOSE US -->
        <section class="mx-auto max-w-7xl px-4 pt-[90px]">
            <h1 class="text-center text-2xl font-semibold">Why Choose Us</h1>
            <p class="mt-2 text-center text-gray-500">
                Beberapa alasan mengapa toko kami jadi pilihan.
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

        <!-- FAQ SECTION -->
        <section class="mx-auto max-w-3xl px-4 py-12">
            <h2 class="text-center text-2xl font-semibold">FAQ</h2>
            <p class="mt-2 text-center text-gray-500">
                Pertanyaan yang sering ditanyakan pelanggan kami.
            </p>

            <div class="mt-8 space-y-4">
                <div
                    v-for="(f, i) in faqs"
                    :key="i"
                    class="rounded-lg border p-4"
                >
                    <button
                        @click="toggle(i)"
                        class="flex w-full items-center justify-between text-left"
                    >
                        <span class="font-medium">{{ f.q }}</span>
                        <span class="text-xl font-bold">
                            {{ openIndex === i ? '−' : '+' }}
                        </span>
                    </button>
                    <p
                        v-if="openIndex === i"
                        class="mt-2 text-sm text-gray-600"
                    >
                        {{ f.a }}
                    </p>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
