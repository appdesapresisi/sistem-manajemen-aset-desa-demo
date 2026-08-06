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
            app(DeviceSessionLimiter::class)->touch(Auth::user(), $request);
        }

        return $next($request);
    }
}
