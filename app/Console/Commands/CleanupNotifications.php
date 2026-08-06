<?php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;

class CleanupNotifications extends Command
{
    /**
     * php artisan notifications:cleanup
     * php artisan notifications:cleanup --days=14
     * php artisan notifications:cleanup --days=30 --unread-days=60
     */
    protected $signature = 'notifications:cleanup
                            {--days=3      : Hapus notifikasi yang sudah dibaca lebih dari N hari}
                            {--unread-days=30 : Hapus notifikasi yang belum dibaca lebih dari N hari}';

    protected $description = 'Hapus notifikasi lama agar tabel notifications tidak menumpuk';

    public function handle(): int
    {
        $readDays   = (int) $this->option('days');
        $unreadDays = (int) $this->option('unread-days');

        if ($readDays <= 0 || $unreadDays <= 0) {
            $this->error('Nilai --days dan --unread-days harus lebih dari 0.');
            return self::FAILURE;
        }

        // Hapus notifikasi sudah dibaca yang lebih tua dari $readDays hari
        $deletedRead = Notification::where('is_read', true)
            ->where('created_at', '<', now()->subDays($readDays))
            ->delete();

        // Hapus notifikasi belum dibaca yang lebih tua dari $unreadDays hari
        $deletedUnread = Notification::where('is_read', false)
            ->where('created_at', '<', now()->subDays($unreadDays))
            ->delete();

        $total = $deletedRead + $deletedUnread;

        $this->info(
            "Selesai cleanup notifikasi: " .
            "sudah_dibaca={$deletedRead} (>{$readDays}h), " .
            "belum_dibaca={$deletedUnread} (>{$unreadDays}h), " .
            "total={$total}."
        );

        return self::SUCCESS;
    }
}
