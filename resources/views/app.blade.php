{{-- resources/views/app.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full scroll-smooth">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- CSRF untuk form/axios --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Preferensi tampilan --}}
    <meta name="color-scheme" content="light">
    <meta name="theme-color" content="#ffffff">

    {{-- Judul dinamis dari Inertia --}}
    <title inertia>{{ config('app.name', 'Laravel') }}</title>

    {{-- (Opsional) Font Breeze --}}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Ziggy untuk route('...') di frontend --}}
    @routes

    {{-- Vite: entry app + halaman Inertia aktif --}}
    @vite(['resources/js/app.js', "resources/js/Pages/{$page['component']}.vue"])

    {{-- Head dari Inertia (meta/title per-halaman) --}}
    @inertiaHead
  </head>
  <body class="min-h-full bg-gray-50 font-sans text-gray-900 antialiased">
    {{-- Root Inertia --}}
    @inertia

    {{-- SweetAlert2 untuk flash & error pada initial load --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Flash: success / status (Breeze sering pakai session("status")) --}}
    @if (session('success') || session('status'))
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: @json(session('success') ?? session('status')),
            showConfirmButton: false,
            timer: 2000
          });
        });
      </script>
    @endif

    {{-- Flash: error --}}
    @if (session('error'))
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          Swal.fire({
            icon: 'error',
            title: 'Terjadi kesalahan',
            text: @json(session('error'))
          });
        });
      </script>
    @endif

    {{-- Flash: warning/info (opsional) --}}
    @if (session('warning') || session('info'))
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          Swal.fire({
            icon: {{ session('warning') ? "'warning'" : "'info'" }},
            title: {{ session('warning') ? "'Perhatian'" : "'Info'" }},
            text: @json(session('warning') ?? session('info'))
          });
        });
      </script>
    @endif

    {{-- Validasi form (MessageBag $errors) --}}
    @if ($errors->any())
      <script>
        document.addEventListener('DOMContentLoaded', () => {
          Swal.fire({
            icon: 'error',
            title: 'Validasi gagal',
            html: `{!! implode('<br>', $errors->all()) !!}`
          });
        });
      </script>
    @endif

    <noscript>
      <div style="padding:16px;text-align:center;background:#fff3cd;color:#664d03">
        JavaScript diperlukan untuk menjalankan aplikasi ini.
      </div>
    </noscript>
  </body>
</html>
