<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const page = usePage();
const user = page.props.auth?.user ?? null;

// highlight aktif (opsional)
const is = (name) => {
    try {
        return route().current(name);
    } catch {
        return false;
    }
};

// admin flag (role === 'admin' atau boolean is_admin)
const isAdmin = computed(() => {
    const u = page.props.auth?.user;
    return !!(u && (u.role === 'admin' || u.is_admin));
});

// SEARCH
const showSearch = ref(false);
const q = ref('');
function submitSearch() {
    if (!q.value) return;
    router.visit(route('shop'), { data: { q: q.value }, preserveState: true });
    showSearch.value = false;
}

// CART
const cartHref = computed(() => (user ? route('shop') : route('login')));

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
    <div class="flex min-h-screen flex-col">
        <!-- NAVBAR -->
        <header class="sticky top-0 z-40 border-b bg-white/80 backdrop-blur">
            <nav
                class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4"
            >
                <!-- Brand -->
                <Link :href="route('landing')" class="flex items-center gap-2">
                    <img
                        src="/theme/images/logo.png"
                        alt="Logo"
                        class="h-6 w-auto"
                    />
                    <span class="font-semibold">My Shop</span>
                </Link>

                <!-- Menu tengah -->
                <ul class="hidden items-center gap-5 text-sm md:flex">
                    <li>
                        <Link
                            :href="route('landing')"
                            :class="{ 'text-indigo-600': is('landing') }"
                            class="hover:text-indigo-600"
                            >Home</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('shop')"
                            :class="{ 'text-indigo-600': is('shop') }"
                            class="hover:text-indigo-600"
                            >Shop</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('why')"
                            :class="{ 'text-indigo-600': is('why') }"
                            class="hover:text-indigo-600"
                            >Why Us</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('testimonial')"
                            :class="{ 'text-indigo-600': is('testimonial') }"
                            class="hover:text-indigo-600"
                            >Testimonials</Link
                        >
                    </li>
                    <li>
                        <Link
                            :href="route('contact')"
                            :class="{ 'text-indigo-600': is('contact') }"
                            class="hover:text-indigo-600"
                            >Contact</Link
                        >
                    </li>
                    <!-- (opsional) Link Admin di menu tengah -->
                    <li v-if="isAdmin">
                        <Link
                            :href="route('admin.dashboard')"
                            class="text-amber-600 hover:text-amber-700"
                            >Admin</Link
                        >
                    </li>
                </ul>

                <!-- Aksi kanan -->
                <div class="flex items-center gap-2">
                    <!-- Search -->
                    <button
                        @click.stop="showSearch = !showSearch"
                        class="rounded-full p-2 hover:bg-gray-100"
                        title="Search"
                        aria-label="Open search"
                    >
                        <img
                            src="/theme/icons/search.svg"
                            alt=""
                            class="h-5 w-5"
                        />
                    </button>

                    <!-- Cart -->
                    <Link
                        :href="cartHref"
                        class="rounded-full p-2 hover:bg-gray-100"
                        :title="user ? 'Your Cart' : 'Login to view cart'"
                        aria-label="Cart"
                    >
                        <img
                            src="/theme/icons/cart.svg"
                            alt=""
                            class="h-5 w-5"
                        />
                    </Link>

                    <!-- Account -->
                    <div class="relative" ref="accountRef">
                        <button
                            @click.stop="toggleAccount"
                            class="rounded-full p-2 hover:bg-gray-100"
                            :title="user ? (user.name ?? 'Account') : 'Account'"
                            aria-haspopup="menu"
                            :aria-expanded="showAccount ? 'true' : 'false'"
                        >
                            <img
                                src="/theme/icons/circle-user.svg"
                                alt="Account"
                                class="h-6 w-6"
                            />
                        </button>

                        <!-- Dropdown -->
                        <transition name="fade">
                            <div
                                v-if="showAccount"
                                class="absolute right-0 mt-2 w-48 overflow-hidden rounded-lg border bg-white shadow-lg"
                                role="menu"
                            >
                                <template v-if="user">
                                    <!-- (opsional) Admin Dashboard di dropdown -->
                                    <Link
                                        v-if="isAdmin"
                                        :href="route('admin.dashboard')"
                                        class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Admin Dashboard
                                    </Link>

                                    <Link
                                        :href="route('profile.edit')"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Edit Profile
                                    </Link>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 hover:bg-rose-50"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        <img
                                            src="/theme/icons/user-logout.svg"
                                            class="h-4 w-4"
                                            alt=""
                                        />
                                        Logout
                                    </Link>
                                </template>

                                <template v-else>
                                    <Link
                                        :href="route('login')"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Login
                                    </Link>
                                    <Link
                                        :href="route('register')"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Register
                                    </Link>
                                </template>
                            </div>
                        </transition>
                    </div>
                </div>
            </nav>

            <!-- Search bar -->
            <transition name="fade">
                <div v-if="showSearch" class="border-b bg-white/90">
                    <div
                        class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-2"
                    >
                        <input
                            v-model="q"
                            type="search"
                            placeholder="Search products…"
                            class="w-full rounded-md border px-3 py-2"
                            @keyup.enter="submitSearch"
                            aria-label="Search products"
                        />
                        <button class="btn-primary" @click="submitSearch">
                            Search
                        </button>
                    </div>
                </div>
            </transition>
        </header>

        <!-- PAGE -->
        <main class="flex-1">
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="mt-16 border-t">
            <div
                class="mx-auto max-w-7xl px-4 py-8 text-center text-sm text-gray-500"
            >
                © 2025 My Shop. All Rights Reserved.
            </div>
        </footer>
    </div>
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
