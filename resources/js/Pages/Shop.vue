<script setup>
import { Head } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
</script>

<template>
    <SiteLayout>
        <Head title="Shop" />
        <section class="mx-auto max-w-7xl px-4 py-10">
            <h1 class="text-2xl font-semibold">Our Products</h1>

            <div
                class="xs:grid-cols-2 mt-6 grid gap-6 sm:grid-cols-3 lg:grid-cols-4"
            >
                <div
                    v-for="i in 8"
                    :key="i"
                    class="overflow-hidden rounded-xl border"
                >
                    <div class="bg-gray-50">
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
