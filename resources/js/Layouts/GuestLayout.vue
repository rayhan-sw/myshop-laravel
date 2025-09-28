<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gray-100 px-4"
    >
        <!-- Card -->
        <div class="w-full max-w-md rounded-lg bg-white p-8 shadow">
            <!-- Logo -->
            <div class="mb-6 flex justify-center">
                <Link :href="route('landing')">
                    <img
                        src="/theme/images/logo.png"
                        alt="Logo"
                        class="h-10 w-auto"
                    />
                </Link>
            </div>

            <!-- Slot untuk form -->
            <slot />
        </div>

        <!-- Footer kecil -->
        <p class="mt-6 text-center text-xs text-gray-500">
            © 2025 My Shop. All Rights Reserved.
        </p>
    </div>
</template>
