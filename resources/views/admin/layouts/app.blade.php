{{-- resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>
      @hasSection('title')
        @yield('title') — Admin — {{ config('app.name', 'Laravel') }}
      @else
        {{ config('app.name', 'Laravel') }} — Admin
      @endif
    </title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
  </head>
  <body class="h-full bg-gray-50 font-sans text-gray-900 antialiased">

    <header class="border-b bg-white" role="banner">
      @include('admin.layouts.partials.header')
    </header>

    <div class="mx-auto flex w-full max-w-7xl gap-6 px-4 py-6">
      <nav class="w-64 shrink-0" aria-label="Admin sidebar">
        @include('admin.layouts.partials.sidebar')
      </nav>

      <main class="min-h-[70vh] flex-1" role="main">
        @hasSection('breadcrumbs')
          <div class="mb-4 text-sm text-gray-500">
            @yield('breadcrumbs')
          </div>
        @endif

        @if(session('success'))
          <div class="mb-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-green-700">
            {{ session('success') }}
          </div>
        @endif
        @if(session('error'))
          <div class="mb-4 rounded-md border border-rose-200 bg-rose-50 px-4 py-3 text-rose-700">
            {{ session('error') }}
          </div>
        @endif
        @if ($errors->any())
          <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
            <div class="font-medium">There were some problems with your input:</div>
            <ul class="mt-2 list-disc ps-5">
              @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
              @endforeach
            </ul>
          </div>
        @endif

        @yield('content')
      </main>
    </div>

    <footer class="border-t bg-white" role="contentinfo">
      <div class="mx-auto max-w-7xl px-4 py-6 text-center text-sm text-gray-500">
        © {{ date('Y') }} Admin Panel — {{ config('app.name', 'Laravel') }}.
      </div>
    </footer>

    @stack('scripts')
  </body>
</html>
