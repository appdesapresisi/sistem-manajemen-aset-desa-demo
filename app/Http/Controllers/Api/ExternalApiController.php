<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\JenisInventaris;
use App\Models\Pejabat;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

/**
 * External API Controller — Read-Only endpoints untuk client external.
 *
 * Autentikasi via Bearer Token (Laravel Sanctum).
 * Hanya menyediakan GET endpoints — tidak bisa create/update/delete.
 *
 * Endpoint ini TIDAK mempengaruhi sistem web internal sama sekali.
 * Route group terpisah di api.php dengan prefix /external.
 */
class ExternalApiController extends Controller
{
    /**
     * POST /api/external/token
     *
     * Generate API token untuk client external.
     * Client kirim username + password, dapat token yang bisa dipakai
     * untuk akses semua endpoint read-only di /api/external/*.
     */
    public function createToken(Request $request)
    {
        $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $request->username)
            ->where('is_aktif', 1)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau password tidak valid.',
            ], 401);
        }

        // Hapus token lama dengan nama 'external-api' milik user ini (max 1 token aktif)
        $user->tokens()->where('name', 'external-api')->delete();

        // Buat token baru dengan kemampuan read-only saja
        $token = $user->createToken('external-api', ['read']);

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil dibuat. Gunakan di header: Authorization: Bearer {token}',
            'token'   => $token->plainTextToken,
            'user'    => [
                'id'       => $user->id,
                'nama'     => $user->nama,
                'username' => $user->username,
                'role'     => $user->role->nama ?? null,
            ],
        ]);
    }

    /**
     * DELETE /api/external/token
     *
     * Revoke token aktif (logout dari API external).
     */
    public function revokeToken(Request $request)
    {
        $request->user()->tokens()->where('name', 'external-api')->delete();

        return response()->json([
            'success' => true,
            'message' => 'Token berhasil dihapus.',
        ]);
    }

    // =========================================================================
    // READ-ONLY ENDPOINTS
    // =========================================================================

    /**
     * GET /api/external/inventaris
     * Daftar semua aset (paginated, bisa filter & search)
     */
    public function inventaris(Request $request)
    {
        $query = Inventaris::with(['jenis:id,nama', 'values.field', 'ruangan:id,nama,kode']);

        if ($request->filled('jenis_inventaris_id')) {
            $query->where('jenis_inventaris_id', $request->jenis_inventaris_id);
        }
        if ($request->filled('kondisi')) {
            $query->where('kondisi', $request->kondisi);
        }
        if ($request->filled('tahun')) {
            $query->where('tahun_perolehan', $request->tahun);
        }
        if ($request->filled('ruangan_id')) {
            $query->where('ruangan_id', $request->ruangan_id);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%")
                  ->orWhere('nup', 'like', "%{$search}%");
            });
        }

        $perPage = max(1, min((int) $request->get('per_page', 15), 100));

        $data = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $items = collect($data->items())->map(fn($inv) => $this->formatInventaris($inv));

        return response()->json([
            'success' => true,
            'data'    => $items,
            'meta'    => [
                'current_page' => $data->currentPage(),
                'per_page'     => $data->perPage(),
                'total'        => $data->total(),
                'last_page'    => $data->lastPage(),
            ],
        ]);
    }

    /**
     * GET /api/external/inventaris/{id}
     * Detail satu aset
     */
    public function inventarisDetail(int $id)
    {
        $inv = Inventaris::with(['jenis:id,nama', 'jenis.fields', 'values.field', 'ruangan:id,nama,kode', 'fotos'])
            ->find($id);

        if (!$inv) {
            return response()->json(['success' => false, 'message' => 'Inventaris tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $this->formatInventaris($inv, true),
        ]);
    }

    /**
     * GET /api/external/jenis-inventaris
     * Daftar semua jenis inventaris
     */
    public function jenisInventaris()
    {
        $data = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')
            ->withCount([
                'inventaris as jumlah_aset',
                'inventaris as kondisi_baik' => fn($q) => $q->where('kondisi', 'baik'),
                'inventaris as kondisi_rusak_ringan' => fn($q) => $q->where('kondisi', 'rusak_ringan'),
                'inventaris as kondisi_rusak_berat' => fn($q) => $q->where('kondisi', 'rusak_berat'),
            ])
            ->withSum('inventaris as total_nilai', 'nilai_perolehan')
            ->orderBy('nama')
            ->get()
            ->map(function ($j) {
                $nilaiAset = (float) ($j->total_nilai ?? 0);

                return [
                    'id'           => $j->id,
                    'nama'         => $j->nama,
                    'deskripsi'    => $j->deskripsi,
                    'icon'         => $j->icon,
                    'kib_type'     => $j->kib_type,
                    'jumlah_aset'  => (int) ($j->jumlah_aset ?? 0),
                    'nilai_aset'   => $nilaiAset,
                    'nilai_format' => 'Rp ' . number_format($nilaiAset, fmod($nilaiAset, 1) != 0 ? 2 : 0, ',', '.'),
                    'kondisi'      => [
                        'baik'         => (int) ($j->kondisi_baik ?? 0),
                        'rusak_ringan' => (int) ($j->kondisi_rusak_ringan ?? 0),
                        'rusak_berat'  => (int) ($j->kondisi_rusak_berat ?? 0),
                    ],
                ];
            });

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * GET /api/external/ruangan
     * Daftar semua ruangan
     */
    public function ruangan()
    {
        $data = Ruangan::withCount('inventaris')
            ->orderBy('nama')
            ->get()
            ->map(fn($r) => [
                'id'              => $r->id,
                'nama'            => $r->nama,
                'kode'            => $r->kode,
                'deskripsi'       => $r->deskripsi,
                'jumlah_aset'     => $r->inventaris_count,
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * GET /api/external/pejabat
     * Daftar pejabat aktif
     */
    public function pejabat()
    {
        $data = Pejabat::where('is_aktif', 1)
            ->orderBy('nama')
            ->get()
            ->map(fn($p) => [
                'id'       => $p->id,
                'nama'     => $p->nama,
                'jabatan'  => $p->jabatan,
                'tugas'    => $p->tugas,
            ]);

        return response()->json(['success' => true, 'data' => $data]);
    }

    /**
     * GET /api/external/statistik
     * Ringkasan statistik aset desa
     */
    public function statistik()
    {
        $totalAset       = Inventaris::count();
        $totalNilai      = Inventaris::sum('nilai_perolehan') ?? 0;
        $kondisiBaik     = Inventaris::where('kondisi', 'baik')->count();
        $kondisiRR       = Inventaris::where('kondisi', 'rusak_ringan')->count();
        $kondisiRB       = Inventaris::where('kondisi', 'rusak_berat')->count();
        $totalJenis      = JenisInventaris::where('nama', '!=', 'Buku Inventaris Aset Desa')->count();
        $totalRuangan    = Ruangan::count();
        $totalPejabat    = Pejabat::where('is_aktif', 1)->count();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_aset'          => $totalAset,
                'total_nilai'         => $totalNilai,
                'total_nilai_format'  => 'Rp ' . number_format($totalNilai, fmod($totalNilai, 1) != 0 ? 2 : 0, ',', '.'),
                'kondisi'             => [
                    'baik'         => $kondisiBaik,
                    'rusak_ringan' => $kondisiRR,
                    'rusak_berat'  => $kondisiRB,
                ],
                'total_jenis'         => $totalJenis,
                'total_ruangan'       => $totalRuangan,
                'total_pejabat_aktif' => $totalPejabat,
            ],
        ]);
    }

    // =========================================================================
    // HELPER
    // =========================================================================

    private function formatInventaris(Inventaris $inv, bool $withFotos = false): array
    {
        $data = [
            'id'               => $inv->id,
            'jenis'            => $inv->jenis ? ['id' => $inv->jenis->id, 'nama' => $inv->jenis->nama] : null,
            'nama_barang'      => $inv->nama_barang,
            'kode_barang'      => $inv->kode_barang,
            'nup'              => $inv->nup,
            'satuan'           => $inv->satuan,
            'tahun_perolehan'  => $inv->tahun_perolehan,
            'nilai_perolehan'  => $inv->nilai_perolehan,
            'asal_usul'        => $inv->asal_usul,
            'pengguna'         => $inv->pengguna,
            'kondisi'          => $inv->kondisi,
            'keterangan'       => $inv->keterangan,
            'ruangan'          => $inv->ruangan ? ['id' => $inv->ruangan->id, 'nama' => $inv->ruangan->nama, 'kode' => $inv->ruangan->kode] : null,
            'dynamic_values'   => $inv->values->map(fn($v) => [
                'field_name' => $v->field->nama_field ?? null,
                'label'      => $v->field->label_field ?? null,
                'value'      => $v->value,
            ])->values(),
            'created_at'       => $inv->created_at?->toIso8601String(),
            'updated_at'       => $inv->updated_at?->toIso8601String(),
        ];

        if ($withFotos) {
            $data['fotos'] = $inv->fotos->map(fn($f) => [
                'id'   => $f->id,
                'url'  => asset('storage/' . $f->path),
                'urutan' => $f->urutan,
            ])->values();
        }

        return $data;
    }
}
