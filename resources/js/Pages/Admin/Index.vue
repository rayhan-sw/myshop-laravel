<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({});

function logout() {
    form.post(route('logout'));
}
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center bg-gray-100"
    >
        <Head title="Admin Index" />

        <h1 class="mb-6 text-3xl font-bold text-gray-800">Halo Admin 👋</h1>

        <!-- Tombol Logout -->
        <button
            @click="logout"
            class="rounded bg-red-600 px-4 py-2 font-semibold text-white hover:bg-red-700"
        >
            Logout
        </button>
    </div>
</template>
