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

        // Level 1: Golongan
        if (isset($segments[0])) {
            $g = KbGolongan::where('kode', $segments[0])->first();
            if ($g) {
                $result[1] = ['id' => $g->id, 'kode' => $g->kode, 'uraian' => $g->uraian];
            }
        }

        // Level 2: Bidang (kode = segments[1], parent = golongan)
        if (isset($segments[1]) && isset($result[1])) {
            $b = KbBidang::where('golongan_id', $result[1]['id'])
                ->where('kode', $segments[1])
                ->first();
            if ($b) {
                $result[2] = ['id' => $b->id, 'kode' => $segments[0].'.'.$b->kode, 'uraian' => $b->uraian];
            }
        }

        // Level 3: Kelompok
        if (isset($segments[2]) && isset($result[2])) {
            $k = KbKelompok::where('bidang_id', $result[2]['id'])
                ->where('kode', $segments[2])
                ->first();
            if ($k) {
                $result[3] = ['id' => $k->id, 'kode' => $segments[0].'.'.$segments[1].'.'.$k->kode, 'uraian' => $k->uraian];
            }
        }

        // Level 4: Sub Kelompok
        if (isset($segments[3]) && isset($result[3])) {
            $sk = KbSubKelompok::where('kelompok_id', $result[3]['id'])
                ->where('kode', $segments[3])
                ->first();
            if ($sk) {
                $result[4] = ['id' => $sk->id, 'kode' => implode('.', array_slice($segments, 0, 4)), 'uraian' => $sk->uraian];
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

        $item = KbSubSubKelompok::where('kode_lengkap', $kode)->first();

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
            $query->where('kode_lengkap', 'LIKE', $ancestorKode . '.%');
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
            $query->whereHas('kelompok.bidang.golongan', function ($qb) use ($ancestorKode) {
                $segments = explode('.', $ancestorKode);
                if (count($segments) >= 1) {
                    $qb->where('kode', $segments[0]);
                }
            });
            if (count(explode('.', $ancestorKode)) >= 2) {
                $segs = explode('.', $ancestorKode);
                $query->whereHas('kelompok.bidang', function ($qb) use ($segs) {
                    $qb->where('kode', $segs[1]);
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
            // Filter by golongan kode (segment 0)
            $query->whereHas('bidang.golongan', function ($qb) use ($segments) {
                $qb->where('kode', $segments[0]);
            });
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
            $query->whereHas('golongan', function ($qb) use ($segments) {
                $qb->where('kode', $segments[0]);
            });
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
