@extends('admin.layouts.app')

@section('content')
  <h1 class="mb-6 text-2xl font-semibold">Dashboard</h1>

  <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
    <div class="rounded-lg border bg-white p-4">
      <div class="text-sm text-gray-500">Total Categories</div>
      <div class="mt-2 text-3xl font-bold">{{ $totalCategories ?? 0 }}</div>
    </div>

    <div class="rounded-lg border bg-white p-4">
      <div class="text-sm text-gray-500">Total Users</div>
      <div class="mt-2 text-3xl font-bold">{{ $totalUsers ?? 0 }}</div>
    </div>

    <div class="rounded-lg border bg-white p-4">
      <div class="text-sm text-gray-500">Last Login</div>
      <div class="mt-2 text-lg">{{ optional(auth()->user()->last_login_at)->diffForHumans() ?? '—' }}</div>
    </div>
  </div>
@endsection
