<?php

namespace App\Http\Controllers\Api;

use App\Models\Bidang;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class BidangController extends Controller
{
    /**
     * Store a newly created Bidang in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Bidang::class);

        try {
            // Validate input
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:bidangs,nama',
            ], [
                'nama.required' => 'Nama bidang harus diisi',
                'nama.max' => 'Nama bidang maksimal 255 karakter',
                'nama.unique' => 'Bidang dengan nama ini sudah ada',
            ]);

            // Create bidang
            $bidang = Bidang::create([
                'nama' => $validated['nama'],
                'deskripsi' => $request->get('deskripsi', null),
            ]);

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $bidang->id,
                    'nama' => $bidang->nama,
                ]
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['nama'][0] ?? 'Validasi gagal',
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat membuat bidang: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display all Bidangs.
     */
    public function index()
    {
        $this->authorize('viewAny', Bidang::class);

        $bidangs = Bidang::orderBy('nama')->get();
        return response()->json([
            'success' => true,
            'data' => $bidangs
        ]);
    }

    /**
     * Display a specific Bidang.
     */
    public function show(Bidang $bidang)
    {
        $this->authorize('view', $bidang);

        return response()->json([
            'success' => true,
            'data' => $bidang->load('users')
        ]);
    }

    /**
     * Update a Bidang in storage.
     */
    public function update(Request $request, Bidang $bidang)
    {
        $this->authorize('update', $bidang);

        try {
            $validated = $request->validate([
                'nama' => 'required|string|max:255|unique:bidangs,nama,' . $bidang->id,
            ]);

            $bidang->update($validated);

            return response()->json([
                'success' => true,
                'data' => $bidang
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->errors()['nama'][0] ?? 'Validasi gagal',
            ], 422);
        }
    }

    /**
     * Delete a Bidang from storage.
     */
    public function destroy(Bidang $bidang)
    {
        $this->authorize('delete', $bidang);

        try {
            // Check if bidang has users
            if ($bidang->users()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tidak dapat menghapus bidang yang masih memiliki pengguna',
                ], 409);
            }

            $bidang->delete();

            return response()->json([
                'success' => true,
                'message' => 'Bidang berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menghapus bidang',
            ], 500);
        }
    }
}
