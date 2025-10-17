{{-- resources/views/admin/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="id" class="h-full">
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

    {{-- Asset utama proyekmu --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')

    {{-- SweetAlert2 (CDN) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11" defer></script>
  </head>
  <body class="h-full bg-gray-50 font-sans text-gray-900 antialiased">
    <div class="min-h-screen">
      {{-- Header global (sekali saja) --}}
      <header class="border-b bg-white" role="banner">
        @include('admin.layouts.partials.header')
      </header>

      <div class="mx-auto flex w-full max-w-7xl gap-6 px-4 py-6">
        {{-- Sidebar kiri (sekali saja) --}}
        @include('admin.layouts.partials.sidebar')

        {{-- Konten utama halaman --}}
        <main class="min-h-[70vh] flex-1" role="main">
          @hasSection('breadcrumbs')
            <div class="mb-4 text-sm text-gray-500">
              @yield('breadcrumbs')
            </div>
          @endif

          {{-- (opsional) fallback error block --}}
          @if ($errors->any())
            <div class="mb-4 rounded-md border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800">
              <div class="font-medium">Ada masalah pada input:</div>
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
    </div>

    {{-- Flash toast (success / warning / error) --}}
    @if (session('success') || session('warning') || session('error'))
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          // Pastikan SweetAlert2 sudah tersedia (script defer)
          const show = () => {
            Swal.fire({
              toast: true,
              position: 'top-end',
              timer: 1800,
              showConfirmButton: false,
              icon: '{{ session('error') ? 'error' : (session('warning') ? 'warning' : 'success') }}',
              title: @json(session('error') ?? session('warning') ?? session('success'))
            });
          };
          // Jika Swal belum ada karena defer, coba sedikit delay
          if (window.Swal) show(); else setTimeout(show, 100);
        });
      </script>
    @endif

    {{-- Konfirmasi default untuk form dengan class helper (opsional) --}}
    <script>
      (function () {
        function bindSwal() {
          document.querySelectorAll('form.js-delete:not([data-swal-bound])').forEach((f) => {
            f.dataset.swalBound = '1';
            f.addEventListener('submit', (e) => {
              e.preventDefault();
              if (!window.Swal) return f.submit();
              Swal.fire({
                title: 'Hapus data ini?',
                text: 'Tindakan ini tidak bisa dibatalkan.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal'
              }).then((res) => { if (res.isConfirmed) f.submit(); });
            }, { capture: true });
          });

          document.querySelectorAll('form.js-edit:not([data-swal-bound])').forEach((f) => {
            f.dataset.swalBound = '1';
            f.addEventListener('submit', (e) => {
              e.preventDefault();
              if (!window.Swal) return f.submit();
              Swal.fire({
                title: 'Simpan perubahan?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Simpan',
                cancelButtonText: 'Batal'
              }).then((res) => { if (res.isConfirmed) f.submit(); });
            }, { capture: true });
          });
        }
        document.addEventListener('DOMContentLoaded', bindSwal);
      })();
    </script>

    @stack('scripts')
  </body>
</html>