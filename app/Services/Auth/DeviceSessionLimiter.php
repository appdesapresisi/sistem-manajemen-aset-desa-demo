<?php

namespace App\Services\Auth;

use App\Models\User;
use App\Models\UserDeviceSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DeviceSessionLimiter
{
    public const DEVICE_COOKIE = 'device_id';

    public const MAX_ACTIVE_DEVICES = 5;

    /**
     * Interval minimum (detik) antara dua DB hit dari touch().
     * Setiap perangkat hanya update last_seen_at ke DB paling cepat setiap 2 menit.
     * Request di antara interval itu diabaikan — tidak ada query DB.
     */
    private const TOUCH_THROTTLE_SECONDS = 120;

    /**
     * Cache static per PHP process untuk hasil Schema::hasTable().
     * Setelah dicek sekali, hasilnya disimpan di sini dan tidak pernah query
     * information_schema lagi selama lifetime proses yang sama.
     */
    private static ?bool $tableExists = null;

    /**
     * Cek apakah tabel user_device_sessions ada — dengan cache static.
     * Query information_schema hanya terjadi sekali per proses PHP.
     */
    private function tableExists(): bool
    {
        if (self::$tableExists === null) {
            self::$tableExists = Schema::hasTable('user_device_sessions');
        }
        return self::$tableExists;
    }

    /**
     * Register current device as active for the user.
     * Returns error message if device limit is reached.
     */
    public function registerOrReject(User $user, Request $request): ?string
    {
        if (!$this->tableExists()) {
            return null;
        }

        try {
            $deviceId = $this->resolveDeviceId($request, true);
            if ($deviceId === null) {
                return null;
            }

            $this->pruneExpiredSessions($user);

            $existing = UserDeviceSession::where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->first();

            if ($existing === null) {
                $activeDeviceCount = UserDeviceSession::where('user_id', $user->id)->count();

                if ($activeDeviceCount >= self::MAX_ACTIVE_DEVICES) {
                    return 'Maksimal 5 perangkat aktif untuk satu akun. Silakan logout dari perangkat lain terlebih dahulu.';
                }

                UserDeviceSession::create($this->buildSessionPayload($user, $request, $deviceId));

                return null;
            }

            $existing->fill($this->buildSessionPayload($user, $request, $deviceId));
            $existing->save();
        } catch (\Throwable $e) {
            Log::warning('Gagal memproses pembatasan sesi perangkat', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return null;
    }

    /**
     * Keep active device heartbeat updated for authenticated requests.
     *
     * Throttled: DB hanya di-hit maksimal setiap TOUCH_THROTTLE_SECONDS detik
     * per kombinasi user+device. Request di antara interval diabaikan tanpa DB query.
     * Throttle state disimpan di session agar ringan dan tidak perlu tabel tambahan.
     */
    public function touch(User $user, Request $request): void
    {
        if (!$this->tableExists()) {
            return;
        }

        try {
            $deviceId = $this->resolveDeviceId($request, true);
            if ($deviceId === null) {
                return;
            }

            // Throttle: cek kapan terakhir kali touch() benar-benar hit DB
            $sessionKey  = 'device_touch_at_' . $user->id . '_' . substr($deviceId, 0, 8);
            $lastTouchAt = $request->session()->get($sessionKey, 0);
            $now         = time();

            if (($now - $lastTouchAt) < self::TOUCH_THROTTLE_SECONDS) {
                // Belum waktunya — skip DB hit
                return;
            }

            // Sudah melewati interval — update DB dan catat waktu touch
            $this->pruneExpiredSessions($user);

            UserDeviceSession::updateOrCreate(
                [
                    'user_id'   => $user->id,
                    'device_id' => $deviceId,
                ],
                $this->buildSessionPayload($user, $request, $deviceId)
            );

            $request->session()->put($sessionKey, $now);
        } catch (\Throwable $e) {
            Log::warning('Gagal memperbarui aktivitas sesi perangkat', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Remove current device record from active devices on logout.
     */
    public function release(User $user, Request $request): void
    {
        if (!$this->tableExists()) {
            return;
        }

        try {
            $deviceId = $this->resolveDeviceId($request, false);
            if ($deviceId === null) {
                return;
            }

            UserDeviceSession::where('user_id', $user->id)
                ->where('device_id', $deviceId)
                ->delete();

            // Bersihkan throttle key dari session saat logout
            $sessionKey = 'device_touch_at_' . $user->id . '_' . substr($deviceId, 0, 8);
            $request->session()->forget($sessionKey);
        } catch (\Throwable $e) {
            Log::warning('Gagal melepas sesi perangkat saat logout', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function buildSessionPayload(User $user, Request $request, string $deviceId): array
    {
        return [
            'user_id'     => $user->id,
            'device_id'   => $deviceId,
            'session_id'  => $request->session()->getId(),
            'ip_address'  => (string) $request->ip(),
            'user_agent'  => (string) $request->userAgent(),
            'last_seen_at' => now(),
        ];
    }

    protected function pruneExpiredSessions(User $user): void
    {
        $sessionLifetime = max((int) config('session.lifetime', 120), 1);

        UserDeviceSession::where('user_id', $user->id)
            ->where('last_seen_at', '<', now()->subMinutes($sessionLifetime + 5))
            ->delete();
    }

    protected function resolveDeviceId(Request $request, bool $queueIfMissing): ?string
    {
        $deviceId = $request->cookie(self::DEVICE_COOKIE);

        if (is_string($deviceId) && preg_match('/^[a-zA-Z0-9\-]{12,64}$/', $deviceId)) {
            return $deviceId;
        }

        if (!$queueIfMissing) {
            return null;
        }

        $newDeviceId = (string) Str::uuid();

        cookie()->queue(cookie()->make(
            self::DEVICE_COOKIE,
            $newDeviceId,
            60 * 24 * 365 * 5,
            null,
            null,
            (bool) config('session.secure', false),
            true,
            false,
            config('session.same_site', 'lax')
        ));

        return $newDeviceId;
    }
}
