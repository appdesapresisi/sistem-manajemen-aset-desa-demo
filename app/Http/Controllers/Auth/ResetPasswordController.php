<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
    public function showResetForm(Request $request, string $token)
    {
        // Fitur reset password via email tidak tersedia — sistem menggunakan username.
        // Reset password dilakukan oleh Super Admin melalui halaman edit user.
        return redirect()->route('login')
            ->with('info', 'Fitur reset password via email tidak tersedia. Hubungi Super Admin.');

        /*
        // ===== KODE LAMA (email-based reset) — DINONAKTIFKAN =====
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email', ''),
        ]);
        // ===== AKHIR KODE LAMA =====
        */
    }

    public function reset(Request $request)
    {
        // Fitur ini dinonaktifkan karena sistem tidak menggunakan email.
        return redirect()->route('login')
            ->with('info', 'Fitur reset password via email tidak tersedia. Hubungi Super Admin.');

        /*
        // ===== KODE LAMA (email-based reset) — DINONAKTIFKAN =====
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'password_confirmation' => ['required'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')
                ->with('status', 'Password berhasil direset. Silakan login dengan password baru.');
        }

        return back()->withErrors([
            'email' => match ($status) {
                Password::INVALID_TOKEN => 'Token reset tidak valid atau sudah kadaluarsa.',
                Password::INVALID_USER  => 'Email tidak ditemukan dalam sistem.',
                default                 => 'Gagal mereset password. Silakan coba lagi.',
            },
        ]);
        // ===== AKHIR KODE LAMA =====
        */
    }
}
