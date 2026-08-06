<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'data', 'url', 'is_read',
    ];

    protected $casts = [
        'data'    => 'array',
        'is_read' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers ──────────────────────────────────────────────

    /** Kirim notif ke satu user. */
    public static function send(int $userId, string $type, string $title, ?string $body = null, array $data = [], ?string $url = null): self
    {
        $url = static::normalizeUrlForStorage($url);

        return static::create([
            'user_id' => $userId,
            'type'    => $type,
            'title'   => $title,
            'body'    => $body,
            'data'    => $data ?: null,
            'url'     => $url,
            'is_read' => false,
        ]);
    }

    /**
     * Simpan URL notifikasi dalam bentuk relatif agar tidak tergantung host/IP.
     */
    private static function normalizeUrlForStorage(?string $url): ?string
    {
        if (!$url) {
            return $url;
        }

        $parsed = parse_url($url);
        if ($parsed === false) {
            return $url;
        }

        $scheme = strtolower((string) ($parsed['scheme'] ?? ''));
        if (!in_array($scheme, ['http', 'https'], true)) {
            return $url;
        }

        $path = $parsed['path'] ?? '';
        if ($path === '') {
            return $url;
        }

        $query = isset($parsed['query']) ? ('?' . $parsed['query']) : '';
        $fragment = isset($parsed['fragment']) ? ('#' . $parsed['fragment']) : '';

        return $path . $query . $fragment;
    }

    /** Kirim notif ke banyak user (array of id). */
    public static function sendToMany(array $userIds, string $type, string $title, ?string $body = null, array $data = [], ?string $url = null): void
    {
        foreach (array_unique($userIds) as $uid) {
            static::send((int) $uid, $type, $title, $body, $data, $url);
        }
    }

    /** Kirim notif ke semua user dengan role tertentu (nama role). */
    public static function sendToRoles(array $roleNames, string $type, string $title, ?string $body = null, array $data = [], ?string $url = null): void
    {
        $userIds = User::whereHas('role', fn($q) => $q->whereIn('nama', $roleNames))
            ->pluck('id')
            ->toArray();
        static::sendToMany($userIds, $type, $title, $body, $data, $url);
    }

    /** Kirim ke semua user aktif. */
    public static function sendToAll(string $type, string $title, ?string $body = null, array $data = [], ?string $url = null): void
    {
        $userIds = User::where('is_aktif', true)->pluck('id')->toArray();
        static::sendToMany($userIds, $type, $title, $body, $data, $url);
    }

    // ── Icon & color helper (untuk view) ─────────────────────

    public function getIconAttribute(): string
    {
        return match($this->type) {
            'jenis_inventaris_created' => 'layers',
            'inventaris_pending'        => 'clock',
            default                    => 'bell',
        };
    }

    public function getColorAttribute(): string
    {
        return match($this->type) {
            'jenis_inventaris_created' => '#8b5cf6',
            default                    => '#6b7280',
        };
    }

    public function getBgColorAttribute(): string
    {
        return match($this->type) {
            'jenis_inventaris_created' => '#ede9fe',
            default                    => '#f3f4f6',
        };
    }
}
