<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Bersihkan token reset password yang sudah kadaluarsa.
        $schedule->command('auth:clear-resets')->hourly();

        // Export disimpan sementara: hapus file export yang lebih tua dari 24 jam.
        $schedule->command('exports:cleanup --hours=24')->hourly();

        // Bersihkan notifikasi lama: sudah dibaca (>3 hari) & belum dibaca (>30 hari).
        $schedule->command('notifications:cleanup --days=3 --unread-days=30')->dailyAt('01:00');

        // Bersihkan activity log secara bertahap: setiap 5 hari, hapus 5 data terlama.
        $schedule->command('activity-logs:cleanup --limit=5')
            ->cron('30 1 */5 * *')
            ->withoutOverlapping();

        // Bersihkan riwayat export secara bertahap: setiap 5 hari, hapus 5 data terlama.
        $schedule->command('export-history:cleanup --limit=5')
            ->cron('35 1 */5 * *')
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
