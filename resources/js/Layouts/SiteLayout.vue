<script setup>
import { Link, usePage, router } from '@inertiajs/vue3';
import { ref, computed, onMounted, onBeforeUnmount } from 'vue';

const page = usePage();
const user = page.props.auth?.user ?? null;
const showMobileMenu = ref(false);

const isDark = ref(false);

function closeAllPopups() {
  showSearch.value = false
  showAccount.value = false
  showMobileMenu.value = false
}

function toggleDarkMode() {
  isDark.value = !isDark.value;
  const html = document.documentElement;
  if (isDark.value) {
    html.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    html.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
}

//klik diluar popup untuk menutup semua popup
onMounted(() => {
  document.addEventListener('click', () => closeAllPopups());
});
onBeforeUnmount(() => {
  document.removeEventListener('click', () => closeAllPopups());
});


onMounted(() => {
  const saved = localStorage.getItem('theme');
  if (saved === 'dark') {
    isDark.value = true;
    document.documentElement.classList.add('dark');
  }
});


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
        <header class= "fixed top-4 left-1/2 z-50 w-[95%] max-w-7xl -translate-x-1/2 
        rounded-2xl border border-white/20 bg-white/60 dark:bg-gray-900/60 
        backdrop-blur-md shadow-md transition-all duration-300">
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

                <!-- Menu Tengah (hanya desktop) -->
                <ul class="hidden md:flex items-center gap-5 text-sm text-gray-700 dark:text-gray-300 font-body">
                    <li><Link :href="route('landing')" :class="{ 'text-brown dark:text-sage': is('landing') }" class="hover:text-sage">Home</Link></li>
                    <li><Link :href="route('shop')" :class="{ 'text-brown dark:text-sage': is('shop') }" class="hover:text-sage">Shop</Link></li>
                    <li><Link :href="route('why')" :class="{ 'text-brown dark:text-sage': is('why') }" class="hover:text-sage">Why Us</Link></li>
                    <li><Link :href="route('testimonial')" :class="{ 'text-brown dark:text-sage': is('testimonial') }" class="hover:text-sage">Testimonials</Link></li>
                    <li><Link :href="route('contact')" :class="{ 'text-brown dark:text-sage': is('contact') }" class="hover:text-sage">Contact</Link></li>
                    <li v-if="isAdmin"><a :href="route('admin.dashboard')" class="text-amber-600 hover:text-amber-700">Admin</a></li>
                </ul>

                <!-- Aksi kanan -->
                <div class="flex items-center gap-2">
                    <!-- Dark Mode Toggle -->
                    <button
                        @click="toggleDarkMode"
                        class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                        title="Toggle dark mode"
                        aria-label="Toggle dark mode"
                        >
                        <!-- Moon (for light mode) -->
                        <svg
                            v-if="!isDark"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75
                                0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25
                                C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z"
                            />
                        </svg>

                        <!-- Sun (for dark mode) -->
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 3v1.5m0 15V21m9-9h-1.5M4.5 12H3
                                m15.364-6.364l-1.06 1.06M7.696 16.304l-1.06 1.06
                                m12.728 0l-1.06-1.06M7.696 7.696L6.636 6.636
                                M12 8.25a3.75 3.75 0 110 7.5 3.75 3.75 0 010-7.5z"
                            />
                        </svg>
                    </button>

                    <!-- Search -->
                    <button
                        @click.stop="
                            if (!showSearch) closeAllPopups();
                            showSearch = !showSearch;
                        "
                        class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                        title="Search"
                        aria-label="Open search"
                    >

                        <svg
                            v-if="!isDark"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                            />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"
                            />
                        </svg>
                        
                    </button>

                    <!-- Cart -->
                    <Link
                        :href="cartHref"
                        class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-700"
                        :title="user ? 'Your Cart' : 'Login to view cart'"
                        aria-label="Cart"
                    >
                        <svg
                            v-if="!isDark"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 
                            3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 
                            0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 
                            0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                            />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 
                            3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 0 
                            0-16.536-1.84M7.5 14.25 5.106 5.272M6 20.25a.75.75 0 1 1-1.5 0 .75.75 
                            0 0 1 1.5 0Zm12.75 0a.75.75 0 1 1-1.5 0 .75.75 0 0 1 1.5 0Z"
                            />
                        </svg>
                    </Link>

                    <!-- Account -->
                    <div class="relative" ref="accountRef">
                        <button
                            @click.stop="
                                if (!showAccount) closeAllPopups();
                                showAccount = !showAccount;
                            "
                            class="rounded-full p-2 hover:bg-gray-100 dark:hover:bg-gray-800"
                            aria-haspopup="menu"
                            aria-expanded="showAccount"
                        >
                            <svg
                                v-if="!isDark"
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-6 w-6 text-gray-700 dark:text-gray-200"
                            >
                                <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 
                                7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                />
                            </svg>
                            <svg
                                v-else
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="h-6 w-6 text-gray-700 dark:text-gray-200"
                            >
                                <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 
                                7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z"
                                />
                            </svg>
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
                                    <a
                                        v-if="isAdmin"
                                        :href="route('admin.dashboard')"
                                        class="block px-4 py-2 text-sm text-amber-700 hover:bg-amber-50 font-body"
                                        role="menuitem"
                                        >
                                        Admin Dashboard
                                    </a>


                                    <Link
                                        :href="route('profile.edit')"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 font-body"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Edit Profile
                                    </Link>
                                    <Link
                                        :href="route('logout')"
                                        method="post"
                                        as="button"
                                        class="flex w-full items-center gap-2 px-4 py-2 text-left text-sm text-rose-600 hover:bg-rose-50 font-body"
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
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 font-body"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Login
                                    </Link>
                                    <Link
                                        :href="route('register')"
                                        class="block px-4 py-2 text-sm hover:bg-gray-50 font-body"
                                        role="menuitem"
                                        @click="showAccount = false"
                                    >
                                        Register
                                    </Link>
                                </template>
                            </div>
                        </transition>
                    </div>

                    <!-- Hamburger (hanya mobile) -->
                    <button
                        @click.stop="
                            if (!showMobileMenu) closeAllPopups();
                            showMobileMenu = !showMobileMenu;
                        "

                        class="rounded-md p-2 hover:bg-gray-100 dark:hover:bg-gray-700 md:hidden font-body"
                        aria-label="Menu"
                        >
                        <svg
                            v-if="!isDark"
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                        <svg
                            v-else
                            xmlns="http://www.w3.org/2000/svg"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="2"
                            stroke="currentColor"
                            class="h-6 w-6 text-gray-700 dark:text-gray-200"
                        >
                            <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                    

                </div>
            </nav>

            <!-- Mobile dropdown menu -->
            <transition name="fade">
                <div v-if="showMobileMenu" class="md:hidden absolute top-14 left-0 w-full border-t bg-white shadow-md z-30">
                    <ul class="flex flex-col gap-2 px-4 py-3 text-sm">
                    <li><Link :href="route('landing')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('landing') }">Home</Link></li>
                    <li><Link :href="route('shop')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('shop') }">Shop</Link></li>
                    <li><Link :href="route('why')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('why') }">Why Us</Link></li>                        <li><Link :href="route('testimonial')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('testimonial') }">Testimonials</Link></li>
                    <li><Link :href="route('contact')" class="block py-2 hover:text-indigo-600" :class="{ 'text-indigo-600': is('contact') }">Contact</Link></li>
                    <li v-if="isAdmin"><Link :href="route('admin.dashboard')" class="block py-2 text-amber-600 hover:text-amber-700">Admin</Link></li>
                    </ul>
                </div>
            </transition>

            <!-- Search bar -->
            <transition name="fade">
            <div
                v-if="showSearch"
                class="border-b bg-white/90 dark:bg-gray-900/90 dark:border-gray-800 backdrop-blur-sm transition-colors"
            >
                <div class="mx-auto flex max-w-7xl items-center gap-2 px-4 py-2">
                <input
                    v-model="q"
                    type="search"
                    placeholder="Search products…"
                    class="w-full rounded-md border border-gray-300 dark:border-gray-700 
                        bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 
                        placeholder-gray-400 dark:placeholder-gray-500 
                        px-3 py-2 transition-colors"
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
        <main class="flex-1 bg-offwhite text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-200">
            <slot />
        </main>

        <!-- FOOTER -->
        <footer class="border-t bg-gray-50 text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-200">
            <div class="mx-auto max-w-7xl px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
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
