<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;

class RuanganApiController extends Controller
{
    /**
     * Return semua ruangan untuk dropdown di form manajemen aset.
     * GET /api/ruangan
     */
    public function index()
    {
        $this->authorize('viewAny', Ruangan::class);

        $ruangans = Ruangan::orderBy('nama')
            ->get(['id', 'nama', 'kode' /*, 'lokasi'*/]);

        return response()->json($ruangans);
    }
}
