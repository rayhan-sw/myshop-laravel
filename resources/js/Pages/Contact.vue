<script setup>
import { Head } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
</script>

<template>
    <SiteLayout>
        <Head title="Contact" />

        <section class="mx-auto max-w-7xl px-4 pt-[90px]">
            <h1 class="text-center text-2xl font-semibold">Contact Us</h1>
            <p class="mt-2 text-center text-gray-500">
                Hubungi kami untuk pertanyaan, saran, atau kerja sama.
            </p>

            <div class="mt-10 grid gap-10 lg:grid-cols-2">
                <!-- Map -->
                <div class="overflow-hidden rounded-lg border shadow">
                    <iframe
                        src="https://maps.google.com/maps?q=jakarta&t=&z=12&ie=UTF8&iwloc=&output=embed"
                        width="100%"
                        height="100%"
                        style="border: 0"
                        loading="lazy"
                        allowfullscreen
                        class="h-[400px] w-full"
                    ></iframe>
                </div>

                <!-- Form -->
                <div class="rounded-lg border bg-white p-6 shadow">
                    <form @submit.prevent class="space-y-4">
                        <input
                            type="text"
                            placeholder="Your Name"
                            class="w-full rounded-md border px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <input
                            type="email"
                            placeholder="Your Email"
                            class="w-full rounded-md border px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <input
                            type="text"
                            placeholder="Phone"
                            class="w-full rounded-md border px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <textarea
                            rows="5"
                            placeholder="Message"
                            class="w-full rounded-md border px-3 py-2 focus:border-indigo-500 focus:ring-indigo-500"
                        />
                        <button class="btn-primary w-full">Send</button>
                    </form>

                    <!-- Info kontak -->
                    <ul class="mt-6 space-y-3 text-sm">
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/images/gifts.png"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>Jl. Contoh No. 123, Jakarta</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/images/free.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/images/high-quality.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>hello@myshop.test</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
