<?php

namespace App\Http\Middleware;

use App\Services\Auth\DeviceSessionLimiter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrackDeviceSession
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();

            if ($user && !$user->is_aktif) {
                try {
                    app(DeviceSessionLimiter::class)->release($user, $request);
                } catch (\Throwable $e) {
                    // ignore
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = 'Akun Anda telah dinonaktifkan oleh administrator. Silakan hubungi Super Admin.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'redirect' => route('login'),
                    ], 403);
                }

                return redirect()->route('login')
                    ->with('error', $message)
                    ->withErrors(['username' => $message]);
            }

            if (app(DeviceSessionLimiter::class)->isDeviceRevoked($user, $request)) {
                try {
                    app(DeviceSessionLimiter::class)->release($user, $request);
                } catch (\Throwable $e) {
                    // ignore
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $message = 'Sesi perangkat Anda telah diakhiri karena pembaruan kata sandi atau logout dari perangkat lain.';

                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => $message,
                        'redirect' => route('login'),
                    ], 401);
                }

                return redirect()->route('login')
                    ->with('error', $message)
                    ->withErrors(['username' => $message]);
            }

            app(DeviceSessionLimiter::class)->touch($user, $request);
        }

        return $next($request);
    }
}
