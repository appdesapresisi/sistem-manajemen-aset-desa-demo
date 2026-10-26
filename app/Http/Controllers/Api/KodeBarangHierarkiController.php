<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\KbGolongan;
use App\Models\KbBidang;
use App\Models\KbKelompok;
use App\Models\KbSubKelompok;
use App\Models\KbSubSubKelompok;
use Illuminate\Http\Request;

class KodeBarangHierarkiController extends Controller
{
    /**
     * GET /api/kb/search?q=...&level=5&parent_id=...&ancestor_kode=...
     *
     * Cari kode barang berdasarkan level hierarki.
     * Level 1 = kb_golongan
     * Level 2 = kb_bidang       (parent_id = golongan.id)
     * Level 3 = kb_kelompok     (parent_id = bidang.id)
     * Level 4 = kb_sub_kelompok (parent_id = kelompok.id)
     * Level 5 = kb_sub_sub_kelompok (default, parent_id = sub_kelompok.id)
     *
     * ancestor_kode: filter by prefix kode (misal '1' untuk semua yang berawalan '1.')
     */
    public function search(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Inventaris::class);

        $q            = trim((string) $request->input('q', ''));
        $level        = (int) $request->input('level', 5);
        $parentId     = $request->input('parent_id') !== null ? (int) $request->input('parent_id') : null;
        $ancestorKode = trim((string) $request->input('ancestor_kode', ''));

        if ($level < 1 || $level > 5) {
            $level = 5;
        }

        switch ($level) {
            case 1: return $this->searchGolongan($q);
            case 2: return $this->searchBidang($q, $parentId, $ancestorKode);
            case 3: return $this->searchKelompok($q, $parentId, $ancestorKode);
            case 4: return $this->searchSubKelompok($q, $parentId, $ancestorKode);
            case 5:
            default: return $this->searchSubSubKelompok($q, $parentId, $ancestorKode);
        }
    }

    /**
     * GET /api/kb/resolve-ancestors?kode=1.01.01
     *
     * Resolve semua ancestor dari sebuah kode.
     * Misal kode=1.01.01 (level 3) → return golongan, bidang, kelompok
     */
    public function resolveAncestors(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Inventaris::class);

        $kode = trim((string) $request->input('kode', ''));
        if ($kode === '') {
            return response()->json(['error' => 'Parameter kode diperlukan.'], 422);
        }

        $segments = array_values(array_filter(explode('.', $kode), fn($s) => trim($s) !== ''));
        $result   = [];

        // Level 1: Golongan (toleran terhadap '1' maupun '01')
        if (isset($segments[0])) {
            $seg0 = $segments[0];
            $g = KbGolongan::whereIn('kode', [$seg0, ltrim($seg0, '0')])->first();
            if ($g) {
                $result[1] = ['id' => $g->id, 'kode' => $g->kode, 'uraian' => $g->uraian];
            }
        }

        // Level 2: Bidang (kode = segments[1], parent = golongan)
        if (isset($segments[1]) && isset($result[1])) {
            $seg1 = $segments[1];
            $b = KbBidang::where('golongan_id', $result[1]['id'])
                ->where(function($q) use ($seg1) {
                    $q->where('kode', $seg1)
                      ->orWhere('kode', str_pad($seg1, 2, '0', STR_PAD_LEFT))
                      ->orWhere('kode', ltrim($seg1, '0'));
                })
                ->first();
            if ($b) {
                $result[2] = ['id' => $b->id, 'kode' => $result[1]['kode'].'.'.$b->kode, 'uraian' => $b->uraian];
            }
        }

        // Level 3: Kelompok
        if (isset($segments[2]) && isset($result[2])) {
            $seg2 = $segments[2];
            $k = KbKelompok::where('bidang_id', $result[2]['id'])
                ->where(function($q) use ($seg2) {
                    $q->where('kode', $seg2)
                      ->orWhere('kode', str_pad($seg2, 2, '0', STR_PAD_LEFT))
                      ->orWhere('kode', ltrim($seg2, '0'));
                })
                ->first();
            if ($k) {
                $result[3] = ['id' => $k->id, 'kode' => $result[2]['kode'].'.'.$k->kode, 'uraian' => $k->uraian];
            }
        }

        // Level 4: Sub Kelompok
        if (isset($segments[3]) && isset($result[3])) {
            $seg3 = $segments[3];
            $sk = KbSubKelompok::where('kelompok_id', $result[3]['id'])
                ->where(function($q) use ($seg3) {
                    $q->where('kode', $seg3)
                      ->orWhere('kode', str_pad($seg3, 2, '0', STR_PAD_LEFT))
                      ->orWhere('kode', ltrim($seg3, '0'));
                })
                ->first();
            if ($sk) {
                $result[4] = ['id' => $sk->id, 'kode' => $result[3]['kode'].'.'.$sk->kode, 'uraian' => $sk->uraian];
            }
        }

        // Level 5: Sub Sub Kelompok (jika segmen level 5 ada)
        if (isset($segments[4]) && isset($result[4])) {
            $seg4 = $segments[4];
            $ssk = KbSubSubKelompok::where('sub_kelompok_id', $result[4]['id'])
                ->where(function($q) use ($seg4) {
                    $q->where('kode', $seg4)
                      ->orWhere('kode', str_pad($seg4, 3, '0', STR_PAD_LEFT))
                      ->orWhere('kode', ltrim($seg4, '0'));
                })
                ->first();
            if ($ssk) {
                $result[5] = ['id' => $ssk->id, 'kode' => $ssk->kode_lengkap, 'uraian' => $ssk->uraian];
            }
        }

        return response()->json($result);
    }

    /**
     * GET /api/kb/by-kode?kode=2.01.01.01.001
     */
    public function getByKode(Request $request)
    {
        $this->authorize('viewAny', \App\Models\Inventaris::class);

        $kode = trim((string) $request->input('kode', ''));

        if ($kode === '') {
            return response()->json(['error' => 'Parameter kode diperlukan.'], 422);
        }

        // Normalisasi kandidat kode (misal '01.01...' vs '1.01...')
        $normalizedKode = preg_replace('/^0+([1-9])/', '$1', $kode);
        $item = KbSubSubKelompok::whereIn('kode_lengkap', array_unique([$kode, $normalizedKode]))->first();

        if (!$item) {
            return response()->json(['error' => 'Kode barang tidak ditemukan.'], 404);
        }

        return response()->json([
            'id'           => $item->id,
            'kode_lengkap' => $item->kode_lengkap,
            'kode'         => $item->kode,
            'uraian'       => $item->uraian,
            'label'        => $item->kode_lengkap . ' — ' . $item->uraian,
        ]);
    }

    // -------------------------------------------------------------------------

    private function searchSubSubKelompok(string $q, ?int $parentId = null, string $ancestorKode = '')
    {
        $query = KbSubSubKelompok::query();

        if ($parentId !== null) {
            $query->where('sub_kelompok_id', $parentId);
        } elseif ($ancestorKode !== '') {
            $normalizedAncestor = preg_replace('/^0+([1-9])/', '$1', $ancestorKode);
            $query->where(function ($qb) use ($ancestorKode, $normalizedAncestor) {
                $qb->where('kode_lengkap', 'LIKE', $ancestorKode . '.%')
                   ->orWhere('kode_lengkap', 'LIKE', $normalizedAncestor . '.%');
            });
        }

        if ($q === '') {
            $items = $query->limit(50)->get();
        } else {
            $items = $query->where(function ($qb) use ($q) {
                $qb->where('uraian', 'LIKE', '%' . $q . '%')
                   ->orWhere('kode_lengkap', 'LIKE', '%' . $q . '%');
            })->limit(50)->get();
        }

        return response()->json(
            $items->map(fn ($item) => [
                'id'           => $item->id,
                'kode_lengkap' => $item->kode_lengkap,
                'kode'         => $item->kode,
                'uraian'       => $item->uraian,
                'label'        => $item->kode_lengkap . ' — ' . $item->uraian,
            ])->values()
        );
    }

    private function searchSubKelompok(string $q, ?int $parentId = null, string $ancestorKode = '')
    {
        $query = KbSubKelompok::with(['kelompok.bidang.golongan']);

        if ($parentId !== null) {
            $query->where('kelompok_id', $parentId);
        } elseif ($ancestorKode !== '') {
            // Filter by ancestor: join ke kelompok→bidang→golongan
            $segments = explode('.', $ancestorKode);
            if (count($segments) >= 1 && trim($segments[0]) !== '') {
                $seg0 = trim($segments[0]);
                $query->whereHas('kelompok.bidang.golongan', function ($qb) use ($seg0) {
                    $qb->whereIn('kode', array_unique([$seg0, ltrim($seg0, '0')]));
                });
            }
            if (count($segments) >= 2 && trim($segments[1]) !== '') {
                $seg1 = trim($segments[1]);
                $query->whereHas('kelompok.bidang', function ($qb) use ($seg1) {
                    $qb->whereIn('kode', array_unique([$seg1, str_pad($seg1, 2, '0', STR_PAD_LEFT), ltrim($seg1, '0')]));
                });
            }
            if (count($segments) >= 3 && trim($segments[2]) !== '') {
                $seg2 = trim($segments[2]);
                $query->whereHas('kelompok', function ($qb) use ($seg2) {
                    $qb->whereIn('kode', array_unique([$seg2, str_pad($seg2, 2, '0', STR_PAD_LEFT), ltrim($seg2, '0')]));
                });
            }
        }

        if ($q !== '') {
            $query->where('uraian', 'LIKE', '%' . $q . '%');
        }

        $items = $query->limit(50)->get();

        return response()->json(
            $items->map(function ($item) {
                $golKode     = $item->kelompok->bidang->golongan->kode ?? '';
                $bidKode     = $item->kelompok->bidang->kode ?? '';
                $kelKode     = $item->kelompok->kode ?? '';
                $subKode     = $item->kode ?? '';
                $kodeLengkap = implode('.', array_filter([$golKode, $bidKode, $kelKode, $subKode], fn ($v) => $v !== ''));

                return [
                    'id'           => $item->id,
                    'kode_lengkap' => $kodeLengkap,
                    'kode'         => $item->kode,
                    'uraian'       => $item->uraian,
                    'label'        => $kodeLengkap . ' — ' . $item->uraian,
                ];
            })->values()
        );
    }

    private function searchKelompok(string $q, ?int $parentId = null, string $ancestorKode = '')
    {
        $query = KbKelompok::with(['bidang.golongan']);

        if ($parentId !== null) {
            $query->where('bidang_id', $parentId);
        } elseif ($ancestorKode !== '') {
            $segments = explode('.', $ancestorKode);
            if (count($segments) >= 1 && trim($segments[0]) !== '') {
                $seg0 = trim($segments[0]);
                $query->whereHas('bidang.golongan', function ($qb) use ($seg0) {
                    $qb->whereIn('kode', array_unique([$seg0, ltrim($seg0, '0')]));
                });
            }
            if (count($segments) >= 2 && trim($segments[1]) !== '') {
                $seg1 = trim($segments[1]);
                $query->whereHas('bidang', function ($qb) use ($seg1) {
                    $qb->whereIn('kode', array_unique([$seg1, str_pad($seg1, 2, '0', STR_PAD_LEFT), ltrim($seg1, '0')]));
                });
            }
        }

        if ($q !== '') {
            $query->where('uraian', 'LIKE', '%' . $q . '%');
        }

        $items = $query->limit(50)->get();

        return response()->json(
            $items->map(function ($item) {
                $golKode     = $item->bidang->golongan->kode ?? '';
                $bidKode     = $item->bidang->kode ?? '';
                $kelKode     = $item->kode ?? '';
                $kodeLengkap = implode('.', array_filter([$golKode, $bidKode, $kelKode], fn ($v) => $v !== ''));

                return [
                    'id'           => $item->id,
                    'kode_lengkap' => $kodeLengkap,
                    'kode'         => $item->kode,
                    'uraian'       => $item->uraian,
                    'label'        => $kodeLengkap . ' — ' . $item->uraian,
                ];
            })->values()
        );
    }

    private function searchBidang(string $q, ?int $parentId = null, string $ancestorKode = '')
    {
        $query = KbBidang::with(['golongan']);

        if ($parentId !== null) {
            $query->where('golongan_id', $parentId);
        } elseif ($ancestorKode !== '') {
            $segments = explode('.', $ancestorKode);
            if (count($segments) >= 1 && trim($segments[0]) !== '') {
                $seg0 = trim($segments[0]);
                $query->whereHas('golongan', function ($qb) use ($seg0) {
                    $qb->whereIn('kode', array_unique([$seg0, ltrim($seg0, '0')]));
                });
            }
        }

        if ($q !== '') {
            $query->where('uraian', 'LIKE', '%' . $q . '%');
        }

        $items = $query->limit(50)->get();

        return response()->json(
            $items->map(function ($item) {
                $golKode     = $item->golongan->kode ?? '';
                $bidKode     = $item->kode ?? '';
                $kodeLengkap = implode('.', array_filter([$golKode, $bidKode], fn ($v) => $v !== ''));

                return [
                    'id'           => $item->id,
                    'kode_lengkap' => $kodeLengkap,
                    'kode'         => $item->kode,
                    'uraian'       => $item->uraian,
                    'label'        => $kodeLengkap . ' — ' . $item->uraian,
                ];
            })->values()
        );
    }

    private function searchGolongan(string $q)
    {
        $query = KbGolongan::query();

        if ($q !== '') {
            $query->where('uraian', 'LIKE', '%' . $q . '%');
        }

        $items = $query->limit(30)->get();

        return response()->json(
            $items->map(fn ($item) => [
                'id'           => $item->id,
                'kode_lengkap' => $item->kode,
                'kode'         => $item->kode,
                'uraian'       => $item->uraian,
                'label'        => $item->kode . ' — ' . $item->uraian,
            ])->values()
        );
    }
}
