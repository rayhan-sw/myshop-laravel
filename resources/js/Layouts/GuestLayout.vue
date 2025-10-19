<script setup>
import { Link } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue'

// Simpan status dark mode
const isDark = ref(false)
onMounted(() => {
  // Baca preferensi dark mode dari localStorage
  //isDark.value = localStorage.theme === 'dark'
  //document.documentElement.classList.toggle('dark', isDark.value)
   // Force-disable dark mode
  document.documentElement.classList.remove('dark')
  localStorage.removeItem('theme')
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
            bg-offwhite text-gray-900 
            dark:bg-gray-900 dark:text-gray-100 
            transition-colors duration-200 px-4 "
    >
        <!-- Card -->
        <div 
            class="w-full max-w-md rounded-lg p-8 shadow-lg
            bg-cream/30 text-gray-900
            dark:bg-darkbrown/40 dark:text-gray-100 dark:shadow-gray-900/40
            transition-colors duration-200"
        >
            <!-- Logo -->
            <div class="mb-6 flex justify-center">
            <Link :href="route('landing')" class="flex items-center gap-2">
                <img
                src="/theme/icons/Vector 1.svg"
                alt="Logo"
                class="h-10 w-auto"
                />
                <span class="font-semibold text-brown dark:text-sage text-lg">
                Second Soul
                </span>
            </Link>
            </div>

            <!-- Slot dengan dark mode berbeda -->
            <div class="p-8 bg-offwhite dark:bg-brown dark:text-gray-100 transition-colors rounded-lg duration-200">
                <slot />
            </div>
            
        </div>

        <!-- Footer kecil -->
        <p class="mt-6 text-center text-xs text-gray-500">
            © 2025 My Shop. All Rights Reserved.
        </p>
    </div>
</template>