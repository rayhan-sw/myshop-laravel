<script setup>
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import SiteLayout from '@/Layouts/SiteLayout.vue';

// zip hanya ada 1 foto orang → pakai sebagai placeholder
const basePhoto = '/theme/images/agency-img.jpg';

const clients = [
    {
        name: 'Tomo Dan Turah',
        role: 'Influencer',
        photo: '/theme/images/tomodanturah.jpg',
        text: 'Kalian semua wajib pada kesini sih!!!! Gokil-gokil bajunya',
    },
    {
        name: 'Livy Dan Jessica Jane',
        role: 'Youtuber dan Tiktoker',
        photo: '/theme/images/livydanjessicajane.jpg',
        text: 'Jujur i suka semua sama produknya, Produknya masih bersih dan wangi-wangi walaupun barang thrift',
    },
    {
        name: 'Jejouw Dan Mohan',
        role: 'Owner THXINSOMNIA dan USS',
        photo: '/theme/images/jejouwdanmohan.jpg',
        text: 'GILAAAA Lu pada wajib kesini breeee, ni toko bakal gw adain di USSFeeds. Lo pada Dateng yeeee',
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

        <section class="w-full px-4 pt-[90px] pb-10">
            <h1 class="text-center text-2xl font-semibold text-brown dark:text-cream">
                What Our Customers Say
            </h1>

            <!-- FRAME -->
            <div
                class="relative mt-8 overflow-hidden rounded-xl border border-sage/40 bg-cream/30 dark:bg-darkbrown/40"
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
                        <div class="mx-auto max-w-7xl text-center">
                            <img
                                :src="c.photo"
                                :alt="c.name"
                                class="mx-auto h-[500px] w-[1500px] rounded-full object-cover"
                            />
                            <p class="mt-4 text-sm italic text-gray-600 dark:text-offwhite">
                                “{{ c.text }}”
                            </p>
                            <p class="mt-2 font-medium text-brown dark:text-cream">{{ c.name }}</p>
                            <p class="text-xs text-gray-500 dark:text-offwhite">{{ c.role }}</p>
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
