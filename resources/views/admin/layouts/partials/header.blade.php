<header class="sticky top-0 z-30 w-full border-b bg-white/80 backdrop-blur">
  <div class="mx-auto flex h-14 max-w-7xl items-center justify-between px-4">
    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2">
      <img src="/theme/images/logo.png" alt="Logo" class="h-6 w-auto">
      <span class="font-semibold">Admin Panel</span>
    </a>

    <nav class="flex items-center gap-3 text-sm">
      <a href="{{ route('landing') }}" class="hover:text-indigo-600">View Site</a>
      <div class="flex items-center gap-2">
        <span class="text-gray-600">{{ auth()->user()->name ?? 'Admin' }}</span>
        <form method="POST" action="{{ route('logout') }}">
          @csrf
          <button class="rounded px-3 py-1 text-rose-600 hover:bg-rose-50">Logout</button>
        </form>
      </div>
    </nav>
  </div>
</header>
