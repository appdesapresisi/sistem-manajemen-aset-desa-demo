<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventaris;
use Illuminate\Support\Facades\Cache;

class KondisiAsetDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengatur 10 aset menjadi 'rusak_ringan' dan 10 aset menjadi 'rusak_berat'.
     */
    public function run(): void
    {
        // 10 Aset untuk Rusak Ringan (Peralatan & Mesin / Perlengkapan Kantor)
        $rrIds = [101, 103, 105, 108, 112, 114, 120, 128, 130, 133];
        Inventaris::whereIn('id', $rrIds)->update(['kondisi' => 'rusak_ringan']);

        // 10 Aset untuk Rusak Berat (Peralatan & Mesin / Perlengkapan Kantor)
        $rbIds = [102, 104, 106, 109, 113, 115, 119, 123, 125, 129];
        Inventaris::whereIn('id', $rbIds)->update(['kondisi' => 'rusak_berat']);

        // Pastikan sisa aset lainnya tetap 'baik'
        Inventaris::whereNotIn('id', array_merge($rrIds, $rbIds))->update(['kondisi' => 'baik']);

        // Clear dashboard cache
        Cache::forget('dashboard_kpi');
        Cache::forget('dashboard_charts');
    }
}
