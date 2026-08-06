<?php

namespace App\Console\Commands;

use App\Models\ActivityLog;
use Illuminate\Console\Command;

class CleanupActivityLogs extends Command
{
    /**
     * php artisan activity-logs:cleanup
     * php artisan activity-logs:cleanup --limit=5
     */
    protected $signature = 'activity-logs:cleanup {--limit=5 : Jumlah data activity log terlama yang dihapus}';

    protected $description = 'Hapus beberapa data activity log terlama secara bertahap';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $cutoff = now()->subDay();

        if ($limit <= 0) {
            $this->error('Nilai --limit harus lebih dari 0.');
            return self::FAILURE;
        }

        $ids = ActivityLog::query()
            ->whereNotNull('created_at')
            ->where('created_at', '<', $cutoff)
            ->orderBy('created_at', 'asc')
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id');

        if ($ids->isEmpty()) {
            $this->info('Tidak ada activity log yang perlu dihapus.');
            return self::SUCCESS;
        }

        $deleted = ActivityLog::whereIn('id', $ids)->delete();

        $this->info("Selesai cleanup activity log: deleted={$deleted}, requested_limit={$limit}, cutoff={$cutoff->format('Y-m-d H:i:s')}.");

        return self::SUCCESS;
    }
}
