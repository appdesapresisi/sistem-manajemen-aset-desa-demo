<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function showForm()
    {
        // Fitur reset password via email tidak tersedia — sistem menggunakan username, bukan email.
        // Super Admin dapat mereset password user langsung dari halaman edit user.
        return view('auth.forgot-password');
    }

    public function sendResetLink(Request $request)
    {
        // Fitur ini dinonaktifkan karena sistem tidak menggunakan email.
        // Reset password dilakukan oleh Super Admin melalui halaman manajemen user.
        return back()->with('status', 'Fitur reset password via email tidak tersedia. Hubungi Super Admin untuk mereset password Anda.');

        /*
        // ===== KODE LAMA (email-based reset) — DINONAKTIFKAN =====
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $credentials = [
            'email' => $request->input('email'),
            'is_aktif' => 1,
        ];

        Password::sendResetLink($credentials);

        return back()->with('status', 'Jika email terdaftar dan akun aktif, link reset password akan dikirim ke email Anda.');
        // ===== AKHIR KODE LAMA =====
        */
    }
}
