<?php

namespace App\Http\Middleware;

use App\Services\Auth\DeviceSessionLimiter;
use Closure;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, Closure $next, ...$guards)
    {
        $this->authenticate($request, $guards);

        $user = $request->user();

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

        return $next($request);
    }

    /**
     * Get the path the user should be redirected to when they are not authenticated.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('login');
    }
}
