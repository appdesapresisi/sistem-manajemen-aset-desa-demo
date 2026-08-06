<?php

namespace App\Http\Controllers\Notification;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /** Halaman daftar semua notifikasi milik user. */
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100])) {
            $perPage = 10;
        }

        $query = Notification::where('user_id', Auth::id());

        // Search filter
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('body', 'like', '%' . $search . '%');
            });
        }

        // Time range filter (Rentang Waktu)
        if ($request->filled('range') && $request->input('range') !== 'all') {
            $range = $request->input('range');
            if ($range === '7d') {
                $query->where('created_at', '>=', now()->subDays(7));
            } elseif ($range === '30d') {
                $query->where('created_at', '>=', now()->subDays(30));
            } elseif ($range === '90d') {
                $query->where('created_at', '>=', now()->subDays(90));
            } elseif ($range === '1y') {
                $query->where('created_at', '>=', now()->subYears(1));
            }
        }

        // Status filter
        if ($request->input('status') === 'unread') {
            $query->where('is_read', false);
        } elseif ($request->input('status') === 'read') {
            $query->where('is_read', true);
        }

        // Sort order
        if ($request->input('sort') === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $notifications = $query->paginate($perPage)->withQueryString();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        $readCount = Notification::where('user_id', Auth::id())
            ->where('is_read', true)
            ->count();

        // Resolve actor names dari data JSON dengan 1 query
        $actorIds = $notifications->getCollection()
            ->map(fn($n) => $n->data['actor_id'] ?? null)
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $actorMap = User::whereIn('id', $actorIds)
            ->pluck('nama', 'id');

        // Inject actor_name ke tiap notif agar mudah dipakai di view
        $notifications->getCollection()->transform(function ($notif) use ($actorMap) {
            if (!empty($notif->data['actor_id'])) {
                $notif->actor_nama = $actorMap[$notif->data['actor_id']] ?? null;
            } else {
                $notif->actor_nama = null;
            }
            return $notif;
        });

        // Group by date label (Hari ini / Kemarin / tanggal)
        $grouped = $notifications->getCollection()->groupBy(function ($notif) {
            if ($notif->created_at->isToday()) return 'Hari ini';
            if ($notif->created_at->isYesterday()) return 'Kemarin';
            return $notif->created_at->translatedFormat('d F Y');
        });

        if ($request->ajax()) {
            $groupedData = [];
            foreach ($grouped as $dateLabel => $items) {
                $itemArray = [];
                foreach ($items as $notif) {
                    $itemArray[] = [
                        'id'             => $notif->id,
                        'title'          => $notif->title,
                        'body'           => $notif->body,
                        'is_read'        => (bool)$notif->is_read,
                        'actor_nama'     => $notif->actor_nama,
                        'date_formatted' => $notif->created_at?->translatedFormat('d M Y'),
                        'time_formatted' => $notif->created_at?->format('H:i'),
                        'read_url'       => route('notifications.read', $notif->id),
                        'destroy_url'    => route('notifications.destroy', $notif->id),
                    ];
                }
                $groupedData[] = [
                    'label' => $dateLabel,
                    'items' => $itemArray,
                ];
            }

            return response()->json([
                'unreadCount' => $unreadCount,
                'readCount'   => $readCount,
                'total'       => $notifications->total(),
                'grouped'     => $groupedData,
                'pagination'  => [
                    'firstItem'   => $notifications->firstItem() ?? 0,
                    'lastItem'    => $notifications->lastItem() ?? 0,
                    'total'       => $notifications->total(),
                    'currentPage' => $notifications->currentPage(),
                    'lastPage'    => $notifications->lastPage(),
                    'hasPages'    => $notifications->hasPages(),
                ],
            ]);
        }

        return view('notifications.index', compact('notifications', 'grouped', 'unreadCount', 'readCount'));
    }

    /** Tandai satu notif sebagai dibaca, lalu redirect ke URL-nya. */
    public function read(Request $request, int $id)
    {
        $notif = Notification::where('user_id', Auth::id())->findOrFail($id);
        $notif->update(['is_read' => true]);

        if ($notif->url) {
            $target = $this->resolveNotificationTarget($notif->url, $request);
            if ($target) {
                return redirect($target);
            }
        }
        return redirect()->route('notifications.index');
    }

    /**
     * Normalisasi URL notifikasi agar aman saat host berubah (LAN/IP/domain berbeda).
     */
    private function resolveNotificationTarget(string $url, Request $request): ?string
    {
        $parsed = parse_url($url);
        if ($parsed === false) {
            return null;
        }

        $host = $parsed['host'] ?? null;
        $path = $parsed['path'] ?? '';
        $query = isset($parsed['query']) ? ('?' . $parsed['query']) : '';
        $fragment = isset($parsed['fragment']) ? ('#' . $parsed['fragment']) : '';

        // URL relatif sudah aman dipakai langsung.
        if (!$host) {
            return $url;
        }

        $currentHost = $request->getHost();
        $appHost = parse_url((string) config('app.url'), PHP_URL_HOST);
        if ($host === $currentHost || ($appHost && $host === $appHost)) {
            return $url;
        }

        // Jika host lama/berbeda tapi path adalah path internal app,
        // pakai path relatif agar tetap membuka halaman yang benar di host aktif.
        if ($path && $this->isInternalAppPath($path)) {
            return $path . $query . $fragment;
        }

        return $url;
    }

    private function isInternalAppPath(string $path): bool
    {
        $prefixes = [
            '/persetujuan',
            '/manajemen-aset',
            '/notifications',
            '/dashboard',
            '/users',
            '/pejabat',
            '/jenis-inventaris',
            '/export-data',
            '/profile',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($path, $prefix)) {
                return true;
            }
        }

        return false;
    }

    /** Tandai semua notif milik user sebagai dibaca (AJAX). */
    public function markAllRead(Request $request)
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->back();
    }

    /** Hapus satu notif. */
    public function destroy(int $id)
    {
        Notification::where('user_id', Auth::id())->findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Notifikasi dihapus.');
    }

    /** Hapus semua notifikasi yang sudah dibaca milik user. */
    public function destroyRead(Request $request)
    {
        $deleted = Notification::where('user_id', Auth::id())
            ->where('is_read', true)
            ->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'deleted' => $deleted,
            ]);
        }

        if ($deleted > 0) {
            return redirect()->back()->with('success', $deleted . ' notifikasi yang sudah dibaca berhasil dihapus.');
        }

        return redirect()->back()->with('info', 'Tidak ada notifikasi yang sudah dibaca untuk dihapus.');
    }

    /** JSON: unread count + 5 notif terbaru (untuk dropdown di header). */
    public function dropdown()
    {
        $unread = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        $items = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn($n) => [
                'id'       => $n->id,
                'type'     => $n->type,
                'title'    => $n->title,
                'body'     => $n->body,
                'url'      => $n->url,
                'is_read'  => $n->is_read,
                'icon'     => $n->icon,
                'color'    => $n->color,
                'bg_color' => $n->bg_color,
                'time'     => $n->created_at?->diffForHumans(null, true),
            ]);

        return response()->json(['unread' => $unread, 'items' => $items]);
    }
}
