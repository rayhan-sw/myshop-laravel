<script setup>
import { Head } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
</script>

<template>
    <SiteLayout>
        <Head title="Shop" />
        <section class="mx-auto max-w-7xl px-4 py-20">
            <h1 class="text-2xl font-semibold">Our Products</h1>

            <div
                class="grid-cols-2 mt-6 grid gap-6 sm:grid-cols-3 lg:grid-cols-4 "
            >
                <div
                    v-for="i in 8"
                    :key="i"
                    class="overflow-hidden rounded-xl border bg-cream/30 dark:bg-gray-900 dark:border-gray-800 transition-colors"
                >
                    <div class="bg-offwhite/70 dark:bg-gray-800 transition hover:opacity-90">
                        <img
                            :src="`/theme/images/p${i}.png`"
                            :alt="`Product ${i}`"
                            class="aspect-square w-full object-contain p-6"
                        />
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium">Product {{ i }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            $ {{ (10 + i).toFixed(2) }}
                        </p>
                        <button class="btn-primary mt-3 w-full">
                            Add to cart
                        </button>
                    </div>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
