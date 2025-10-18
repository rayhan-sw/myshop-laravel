<!-- Tombol toggle sidebar (muncul hanya di tampilan mobile) -->
<label for="sidebar-toggle"
       class="fixed top-4 left-4 z-50 md:hidden p-2 bg-white rounded-md shadow-md cursor-pointer">
    <svg class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</label>

<!-- Checkbox kontrol sidebar (dipakai untuk toggle di mobile) -->
<input type="checkbox" id="sidebar-toggle" class="hidden peer">

<!-- Sidebar navigasi utama admin -->
<aside class="fixed inset-y-0 left-0 w-64 transform -translate-x-full transition-transform duration-200 ease-in-out
               bg-white border-r p-4 z-40
               peer-checked:translate-x-0 md:translate-x-0 md:static md:flex-shrink-0">

    <!-- Tombol tutup sidebar (khusus mobile) -->
    <label for="sidebar-toggle" class="absolute top-4 right-4 cursor-pointer md:hidden">✕</label>

    <!-- Menu navigasi -->
    <ul class="space-y-1 text-sm mt-20">
        <li>
            <a href="{{ route('admin.dashboard') }}" 
               class="block rounded px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.dashboard') ? 'bg-gray-100 font-medium' : '' }}">
               Dashboard
            </a>
        </li>
        <li>
            <a href="{{ route('admin.categories.index') }}" 
               class="block rounded px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.categories.*') ? 'bg-gray-100 font-medium' : '' }}">
               Categories
            </a>
        </li>
        <li>
            <a href="{{ route('admin.products.index') }}" 
               class="block rounded px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.products.*') ? 'bg-gray-100 font-medium' : '' }}">
               Products
            </a>
        </li>
        <li>
            <a href="{{ route('admin.orders.index') }}" 
               class="block rounded px-3 py-2 hover:bg-gray-100 {{ request()->routeIs('admin.orders.*') ? 'bg-gray-100 font-medium' : '' }}">
               Orders
            </a>
        </li>
    </ul>
</aside>
