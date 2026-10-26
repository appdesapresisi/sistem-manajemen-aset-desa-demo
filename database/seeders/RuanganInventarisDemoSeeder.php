<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Inventaris;
use App\Models\Ruangan;
use Illuminate\Support\Facades\Cache;

class RuanganInventarisDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Mengalokasikan penempatan ruangan secara dinamis & realistis untuk aset Peralatan & Mesin,
     * memastikan setiap ruangan di sistem memiliki data aset.
     */
    public function run(): void
    {
        $ruangans = Ruangan::all();
        if ($ruangans->isEmpty()) {
            return;
        }

        // Ambil inventaris kategori Peralatan dan Mesin (jenis_inventaris_id = 30)
        $items = Inventaris::where('jenis_inventaris_id', 30)->orderBy('id')->get();
        if ($items->isEmpty()) {
            return;
        }

        $allRuanganIds = $ruangans->pluck('id')->values()->toArray();
        $ruanganCount = count($allRuanganIds);

        // Helper untuk mencari ID ruangan berdasarkan kata kunci nama ruangan
        $findRuanganId = function (array $keywords) use ($ruangans) {
            foreach ($ruangans as $r) {
                $rNama = strtolower($r->nama);
                foreach ($keywords as $kw) {
                    if (str_contains($rNama, strtolower($kw))) {
                        return $r->id;
                    }
                }
            }
            return null;
        };

        // Pemetaan kata kunci aset ke kata kunci ruangan
        $rules = [
            ['keywords' => ['perpustakaan', 'katalog', 'buku', 'rak buku'], 'target' => ['perpustakaan']],
            ['keywords' => ['bpd', 'sidang'], 'target' => ['bpd']],
            ['keywords' => ['linmas', 'keamanan', 'jaga', 'senter', 'borgol'], 'target' => ['linmas', 'keamanan', 'pos']],
            ['keywords' => ['pkk', 'dapur', 'kompor', 'kulkas', 'dispenser'], 'target' => ['pkk', 'pemberdayaan']],
            ['keywords' => ['aula', 'podium', 'sound', 'proyektor', 'speaker'], 'target' => ['aula', 'pertemuan']],
            ['keywords' => ['gudang', 'tenda', 'rumput', 'taman', 'tangga', 'genset'], 'target' => ['gudang']],
            ['keywords' => ['perbekel', 'kades', 'lurah', 'pimpinan'], 'target' => ['perbekel', 'kepala']],
            ['keywords' => ['sekdes', 'sekretaris', 'brankas'], 'target' => ['sekdes', 'sekretaris']],
            ['keywords' => ['pelayanan', 'loket', 'antrian', 'paten'], 'target' => ['pelayanan', 'paten']],
            ['keywords' => ['posyandu', 'timbangan', 'stetoskop', 'kesehatan', 'tensi'], 'target' => ['posyandu', 'kesehatan']],
            ['keywords' => ['vip', 'sofa', 'tamu'], 'target' => ['vip', 'tamu', 'rapat']],
            ['keywords' => ['staf', 'arsip', 'filling', 'komputer', 'printer', 'meja', 'kursi'], 'target' => ['staf', 'kaur', 'kasi']],
        ];

        // Sisakan ~15 aset tanpa ruangan agar data audit "Kelengkapan Aset" tetap realistis
        $unassignedThreshold = 15;
        $totalItems = $items->count();
        $assignableItems = $items->slice(0, max(0, $totalItems - $unassignedThreshold));

        // 1. Reset ruangan pada aset yang akan dialokasikan
        $items->slice($totalItems - $unassignedThreshold)->each(function ($item) {
            $item->update(['ruangan_id' => null]);
        });

        // 2. Alokasikan berdasarkan aturan kecocokan nama aset
        $poolIndex = 0;
        foreach ($assignableItems as $item) {
            $nama = strtolower($item->nama_barang);
            $matchedRuanganId = null;

            foreach ($rules as $rule) {
                foreach ($rule['keywords'] as $kw) {
                    if (str_contains($nama, $kw)) {
                        $matchedRuanganId = $findRuanganId($rule['target']);
                        if ($matchedRuanganId) break 2;
                    }
                }
            }

            // Jika tidak cocok aturan spesifik, distribusikan secara merata ke seluruh ruangan yang ada
            if (!$matchedRuanganId) {
                $matchedRuanganId = $allRuanganIds[$poolIndex % $ruanganCount];
                $poolIndex++;
            }

            $item->update(['ruangan_id' => $matchedRuanganId]);
        }

        // 3. Pastikan SEMUA ruangan memiliki setidaknya 3-5 aset
        foreach ($ruangans as $ruangan) {
            $currentCount = Inventaris::where('ruangan_id', $ruangan->id)->count();
            if ($currentCount < 3) {
                $needed = 4 - $currentCount;
                // Ambil aset dari ruangan yang memiliki aset terbanyak (> 15 aset)
                $donorItems = Inventaris::whereNotNull('ruangan_id')
                    ->where('ruangan_id', '!=', $ruangan->id)
                    ->inRandomOrder()
                    ->limit($needed)
                    ->get();

                foreach ($donorItems as $donor) {
                    $donor->update(['ruangan_id' => $ruangan->id]);
                }
            }
        }

        // Clear dashboard cache
        Cache::forget('dashboard_kpi');
        Cache::forget('dashboard_charts');
    }
}
