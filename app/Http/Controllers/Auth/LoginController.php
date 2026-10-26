<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\Auth\DeviceSessionLimiter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        // Redirect to dashboard if user already authenticated
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginIdentifier = $data['username'];

        // Try login by `username` first, then fallback to `email` if input looks like an email
        $attemptCredentials = [
            'username' => $loginIdentifier,
            'password' => $data['password'],
            'is_aktif' => 1,
        ];

        $attempted = Auth::attempt($attemptCredentials, $request->boolean('remember'));

        if (! $attempted && filter_var($loginIdentifier, FILTER_VALIDATE_EMAIL)) {
            $attemptCredentials = [
                'email' => $loginIdentifier,
                'password' => $data['password'],
                'is_aktif' => 1,
            ];

            $attempted = Auth::attempt($attemptCredentials, $request->boolean('remember'));
        }

        if ($attempted) {
            $request->session()->regenerate();

            $deviceLimitMessage = app(DeviceSessionLimiter::class)
                ->registerOrReject(Auth::user(), $request);

            if ($deviceLimitMessage !== null) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $deviceLimitMessage,
                        'errors' => [
                            'username' => [$deviceLimitMessage],
                        ],
                    ], 422);
                }

                return back()->withErrors(['username' => $deviceLimitMessage])->withInput();
            }

            // Flash nama user untuk toast welcome di dashboard
            session()->flash('success', 'Selamat datang, ' . auth()->user()->nama . '!');

            // Return JSON for AJAX requests
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Login berhasil!',
                    'redirect' => route('dashboard')
                ]);
            }

            return redirect()->intended(route('dashboard'));
        }

        // Login failed
        $errorMessage = 'Username atau password tidak sesuai. Pastikan akun Anda aktif.';
        
        if ($request->expectsJson()) {
            return response()->json([
                'success' => false,
                'message' => $errorMessage,
                'errors' => [
                    'username' => [$errorMessage],
                    'password' => [$errorMessage],
                ]
            ], 422);
        }

        return back()->withErrors([
            'username' => $errorMessage,
            'password' => $errorMessage,
        ])->withInput();
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        // Ambil nama sebelum session di-invalidate
        $nama = $user?->nama ?? '';

        try {
            if ($user !== null) {
                app(DeviceSessionLimiter::class)->release($user, $request);
            }

            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } catch (\Throwable $e) {
            Log::error('Logout failed', ['error' => $e->getMessage()]);
        }

        // Flash nama untuk toast logout di halaman login
        session()->flash('success', 'Sampai jumpa, ' . $nama . '! Anda telah keluar dari sistem.');

        return redirect()->route('login');
    }
}
