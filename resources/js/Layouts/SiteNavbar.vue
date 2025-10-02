<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const page = usePage();
const authUser = page.props.auth?.user ?? null;
const showMobileMenu = ref(false);


// route highlight (opsional)
const is = (name) => {
    try {
        return route().current(name);
    } catch {
        return false;
    }
};

// SEARCH
const showSearch = ref(false);
const q = ref('');
function submitSearch() {
    if (!q.value) return;
    router.visit(route('shop'), { data: { q: q.value }, preserveState: true });
    showSearch.value = false;
}

// CART
const cartHref = computed(() => (authUser ? route('shop') : route('login')));

// ACCOUNT DROPDOWN
const showAccount = ref(false);
const accountRef = ref(null);
function toggleAccount() {
    showAccount.value = !showAccount.value;
}
function clickOutside(e) {
    if (!accountRef.value) return;
    if (!accountRef.value.contains(e.target)) showAccount.value = false;
}
onMounted(() => document.addEventListener('click', clickOutside));
onBeforeUnmount(() => document.removeEventListener('click', clickOutside));
</script>

<template>
    <header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur relative">
        <nav class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4">
            <!-- Brand -->
            <Link :href="route('landing')" class="flex items-center gap-2">
            <img src="/theme/images/logo.png" alt="Logo" class="h-6 w-auto" />
            <span class="font-semibold">My SHoppe</span>
            </Link>

            <!-- Mobile menu button -->
            <button
            @click="showMobileMenu = !showMobileMenu"
            class="md:hidden rounded p-2 hover:bg-gray-100"
            aria-label="Toggle menu"
            :aria-expanded="showMobileMenu.toString()"
            >
            <!-- Hamburger icon -->
            <svg v-if="!showMobileMenu" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 12h16M4 18h16"/>
            </svg>
            <!-- Close icon -->
            <svg v-else xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24" stroke="currentColor" class="h-6 w-6">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M6 18L18 6M6 6l12 12"/>
            </svg>
            </button>

            <!-- Desktop menu -->
            <ul class="hidden md:flex items-center gap-4 text-sm">
            <li><Link :href="route('landing')" :class="{ 'text-indigo-600': is('landing') }" class="hover:text-indigo-600">Home</Link></li>
            <li><Link :href="route('shoppe')" :class="{ 'text-indigo-600': is('shop') }" class="hover:text-indigo-600">Shop</Link></li>
            <li><Link :href="route('why')" :class="{ 'text-indigo-600': is('why') }" class="hover:text-indigo-600">Why Us</Link></li>
            <li><Link :href="route('testimonial')" :class="{ 'text-indigo-600': is('testimonial') }" class="hover:text-indigo-600">Testimonials</Link></li>
            <li><Link :href="route('contact')" :class="{ 'text-indigo-600': is('contact') }" class="hover:text-indigo-600">Contact</Link></li>
            </ul>

            <!-- Right actions (search, cart, account) -->
            <div class="flex items-center gap-2">
            <!-- your buttons unchanged -->
            </div>
        </nav>

        <!-- Mobile dropdown menu (place it AFTER nav) -->
        <transition name="fade">
            <div v-if="showMobileMenu" class="md:hidden border-t bg-white">
            <ul class="flex flex-col gap-2 px-4 py-3 text-sm">
                <li><Link :href="route('landing')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('landing') }">Home</Link></li>
                <li><Link :href="route('shop')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('shop') }">Shop</Link></li>
                <li><Link :href="route('why')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('why') }">Why Us</Link></li>
                <li><Link :href="route('testimonial')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('testimonial') }">Testimonials</Link></li>
                <li><Link :href="route('contact')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('contact') }">Contact</Link></li>
            </ul>
            </div>
        </transition>
    </header>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.15s ease;
}
.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
