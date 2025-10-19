<script setup>
import { Head } from '@inertiajs/vue3';
import SiteLayout from '@/Layouts/SiteLayout.vue';
</script>

<template>
    <SiteLayout>
        <Head title="Contact" />

        <section class="mx-auto max-w-7xl px-4 pt-[90px] pb-10">
            <h1 class="text-center text-2xl font-semibold text-brown dark:text-cream">Find Us!</h1>
            <p class="mt-2 text-center text-gray-500 dark:text-offwhite">
                Hubungi kami untuk pertanyaan, saran, atau kerja sama.
            </p>

            <div class="mt-10 grid gap-10">
                <!-- Map -->
                <div class="overflow-hidden rounded-lg border shadow">
                    <iframe
                        src="https://maps.google.com/maps?q=Pasar%20Senen%2C%20Jakarta&t=&z=15&ie=UTF8&iwloc=&output=embed"
                        width="100%"
                        height="100%"
                        style="border:0"
                        loading="lazy"
                        allowfullscreen
                        class="h-[400px] w-full"
                    ></iframe>

                </div>

                <!-- Form -->
                <div class="rounded-lg border-sage/40 bg-cream/30 dark:bg-darkbrown/40 p-6 shadow">
                    <!-- Info kontak -->
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/icons/gmapslogo.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>Pasar Senen Blok A, Lantai 3 No. 12
                            Jl. St. Senen No. 135, Kelurahan Senen, Kecamatan Senen
                            Jakarta Pusat, DKI Jakarta 10410
                            Indonesia</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/icons/WhatsApp.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>+62 812-3456-7890</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/icons/gmaillogo.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>secondsoul@gmail.com</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/icons/tiktoklogo.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>SecondSoul.TikTok</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <img
                                src="/theme/icons/instalogo.svg"
                                class="h-5 w-5"
                                alt=""
                            />
                            <span>SecondSoul.Insta</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>
    </SiteLayout>
</template>
