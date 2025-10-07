<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'

// Simpan status dark mode
const isDark = ref(false)
onMounted(() => {
  // Baca preferensi dark mode dari localStorage
  isDark.value = localStorage.theme === 'dark'
  document.documentElement.classList.toggle('dark', isDark.value)
})

function toggleDarkMode() {
  isDark.value = !isDark.value
  document.documentElement.classList.toggle('dark', isDark.value)
  localStorage.theme = isDark.value ? 'dark' : 'light'
}
</script>

<template>
    <div
        class="flex min-h-screen flex-col items-center justify-center 
            bg-gray-100 text-gray-900 
            dark:bg-gray-900 dark:text-gray-100 
            transition-colors duration-200 px-4"
    >
        <!-- Card -->
        <div 
            class="w-full max-w-md rounded-lg p-8 shadow-lg
            bg-white text-gray-900
            dark:bg-gray-800 dark:text-gray-100 dark:shadow-gray-900/40
            transition-colors duration-200"
        >
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

            <!-- Slot dengan dark mode berbeda -->
            <div class="p-8 bg-gray-50 dark:bg-gray-700 dark:text-gray-100 transition-colors duration-200">
                <slot />
            </div>
            
        </div>

        <!-- Footer kecil -->
        <p class="mt-6 text-center text-xs text-gray-500">
            © 2025 My Shop. All Rights Reserved.
        </p>
    </div>
</template>
