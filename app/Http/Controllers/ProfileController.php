<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    // Tampilkan form profil user (Inertia)
    public function edit(Request $request): Response
    {
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail, // info apakah perlu verifikasi email
            'status' => session('status'),  // flash status (opsional)
        ]);
    }

    // Perbarui informasi profil pengguna
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated()); // isi field yang lolos validasi

        // Jika email berubah, reset status verifikasi
        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save(); // simpan perubahan

        return Redirect::route('profile.edit'); // kembali ke halaman edit
    }

    // Hapus akun pengguna secara permanen
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'], // pastikan password saat ini benar
        ]);

        $user = $request->user();

        Auth::logout(); // logout terlebih dahulu
        $user->delete(); // hapus akun
        $request->session()->invalidate(); // invalidasi sesi
        $request->session()->regenerateToken(); // ganti CSRF token

        return Redirect::to('/'); // arahkan ke halaman utama
    }
}
