<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// zip hanya ada 1 foto orang → pakai sebagai placeholder
const basePhoto = '/theme/images/agency-img.jpg';

const clients = [
    {
        name: 'Sinta',
        role: 'Customer',
        photo: basePhoto,
        text: 'Produk bagus dan cepat sampai!',
    },
    {
        name: 'Budi',
        role: 'Customer',
        photo: basePhoto,
        text: 'Pelayanan ramah, recommended.',
    },
    {
        name: 'Andi',
        role: 'Customer',
        photo: basePhoto,
        text: 'Harga pas, kualitas oke!',
    },
    {
        name: 'Rina',
        role: 'Customer',
        photo: basePhoto,
        text: 'Packing rapi, aman sampai.',
    },
    {
        name: 'Doni',
        role: 'Customer',
        photo: basePhoto,
        text: 'Respon cepat, mantap.',
    },
    {
        name: 'Vina',
        role: 'Customer',
        photo: basePhoto,
        text: 'Pas di kantong, kualitas sip.',
    },
];

const index = ref(0);
const total = clients.length;

// Track hanya perlu transform; width biarkan auto (hasil penjumlahan slide)
const styleTrack = computed(() => ({
    transform: `translateX(-${index.value * 100}%)`,
}));

function next() {
    index.value = (index.value + 1) % total;
}
function prev() {
    index.value = (index.value - 1 + total) % total;
}
function go(i) {
    index.value = i;
}
</script>

<template>
    <SiteLayout>
        <Head title="Testimonial" />

        <section class="mx-auto max-w-3xl px-4 pt-[90px]">
            <h1 class="text-center text-2xl font-semibold">
                What Our Customers Say
            </h1>

            <!-- FRAME -->
            <div
                class="relative mt-8 overflow-hidden rounded-xl border bg-white"
            >
                <!-- TRACK (jangan set width) -->
                <div
                    class="flex transition-transform duration-500 ease-out"
                    :style="styleTrack"
                >
                    <!-- SLIDE (min-w-full + shrink-0 agar 1 layar per slide) -->
                    <div
                        v-for="(c, i) in clients"
                        :key="i"
                        class="min-w-full shrink-0 p-8"
                    >
                        <div class="mx-auto max-w-xl text-center">
                            <img
                                :src="c.photo"
                                :alt="c.name"
                                class="mx-auto h-20 w-20 rounded-full object-cover"
                            />
                            <p class="mt-4 text-sm italic text-gray-600">
                                “{{ c.text }}”
                            </p>
                            <p class="mt-2 font-medium">{{ c.name }}</p>
                            <p class="text-xs text-gray-500">{{ c.role }}</p>
                        </div>
                    </div>
                </div>

                <!-- CONTROLS -->
                <button
                    @click="prev"
                    class="absolute left-3 top-1/2 -translate-y-1/2 rounded-full border bg-white/80 px-3 py-1 text-lg hover:bg-white"
                    aria-label="Previous"
                >
                    ‹
                </button>
                <button
                    @click="next"
                    class="absolute right-3 top-1/2 -translate-y-1/2 rounded-full border bg-white/80 px-3 py-1 text-lg hover:bg-white"
                    aria-label="Next"
                >
                    ›
                </button>
            </div>

            <!-- INDICATORS -->
            <div class="mt-4 flex justify-center gap-2">
                <button
                    v-for="(c, i) in clients"
                    :key="i"
                    @click="go(i)"
                    :class="[
                        'h-2 w-2 rounded-full transition-colors',
                        i === index
                            ? 'bg-indigo-600'
                            : 'bg-gray-300 hover:bg-gray-400',
                    ]"
                    aria-label="Go to slide"
                />
            </div>
        </section>
    </SiteLayout>
</template>
