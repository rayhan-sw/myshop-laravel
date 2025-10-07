<aside class="hidden w-64 flex-shrink-0 border-r bg-white/90 p-4 md:block">
  <ul class="space-y-1 text-sm">
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
  </ul>
</aside>
