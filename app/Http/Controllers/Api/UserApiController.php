<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\PejabatService;
use App\Services\ActivityLogger;

class UserApiController extends Controller
{
    protected $pejabatService;

    public function __construct(PejabatService $pejabatService)
    {
        $this->pejabatService = $pejabatService;
    }

    /**
     * Display a listing of users
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        // Log::debug('API: fetching users list', ['user' => auth()->id()]);

        $isSelfSuperDev = $request->user() && $request->user()->isSuperDeveloper();
        $query = $isSelfSuperDev
            ? User::with(['pejabat', 'role:id,nama'])
            : User::excludeSuperDevelopers()->with(['pejabat', 'role:id,nama']);

        // Filter by role
        if ($request->has('role')) {
            $roleName = trim((string) $request->role);
            if ($roleName !== '') {
                $query->whereHas('role', function ($q) use ($roleName) {
                    $q->where('nama', $roleName);
                });
            }
        }

        // Search
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
                  // ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $perPage = max(1, min((int) $request->get('per_page', 10), 100));
        $users = $query->latest()->paginate($perPage);

        // Remove sensitive data
        foreach ($users as $user) {
            unset($user->password);
        }

        return response()->json($users);
    }

    /**
     * Store a newly created user
     */
    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->validated();

        if (!empty($data['role']) && $data['role'] === 'super_developer') {
            return response()->json(['success' => false, 'message' => 'Role Super Developer tidak dapat ditambahkan.'], 422);
        }

        $superDevUsernames = array_map('strtolower', User::getSuperDeveloperUsernames());
        if (in_array(strtolower($data['username'] ?? ''), $superDevUsernames, true)) {
            return response()->json(['success' => false, 'message' => 'Username tersebut dicadangkan untuk sistem dan tidak dapat digunakan.'], 422);
        }

        // Validasi: super_admin hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'super_admin') {
            if (User::excludeSuperDevelopers()->whereHas('role', fn($q) => $q->where('nama', 'super_admin'))->exists()) {
                return response()->json(['success' => false, 'message' => 'Sudah ada Super Admin. Hanya 1 Super Admin yang diperbolehkan.'], 422);
            }
        }
        // Validasi: perbekel hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'perbekel') {
            if (User::excludeSuperDevelopers()->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->exists()) {
                return response()->json(['success' => false, 'message' => 'Sudah ada user dengan role Perbekel. Hanya 1 user yang diperbolehkan.'], 422);
            }
        }
        // Validasi: sekretaris desa hanya boleh 1 user
        if (!empty($data['role']) && stripos((string) $data['role'], 'sekretaris') !== false) {
            if (User::excludeSuperDevelopers()->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists()) {
                return response()->json(['success' => false, 'message' => 'Sudah ada user dengan role Sekretaris Desa. Hanya 1 user yang diperbolehkan.'], 422);
            }
        }

        // Upload TTD di luar transaction agar bisa dihapus saat rollback
        $uploadedTtdPath = null;
        if (!empty($data['create_pejabat']) && $request->hasFile('ttd_path')) {
            try {
                $uploadedTtdPath = $request->file('ttd_path')->store('ttd', 'public');
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => 'Gagal mengupload file tanda tangan.'], 500);
            }
        }

        try {
            DB::beginTransaction();
            
            // Create user
            $userData = [
                'nama' => $data['nama'],
                'username' => $data['username'],
                // 'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'is_aktif' => $data['is_aktif'] ?? 1,
                'alamat' => $data['alamat'] ?? null,
            ];

            if (!empty($data['role'])) {
                $roleModel = Role::where('nama', $data['role'])->first();
                if ($roleModel) {
                    $userData['role_id'] = $roleModel->id;
                }
            }

            // handle bidang_id if role is kaur, kasi, or staf
            if (!empty($data['role']) && (stripos($data['role'], 'kaur') !== false || stripos($data['role'], 'kasi') !== false || stripos($data['role'], 'staf') !== false)) {
                if (!empty($data['bidang_id'])) {
                    $userData['bidang_id'] = $data['bidang_id'];
                }
            }

            // handle dinas_id if role is kelian_banjar
            if (!empty($data['role']) && stripos($data['role'], 'kelian_banjar') !== false) {
                if (!empty($data['dinas_id'])) {
                    $userData['dinas_id'] = $data['dinas_id'];
                }
            }
            
            $user = User::create($userData);

            // Create pejabat if requested — pakai createWithoutUpload karena TTD sudah di-upload di atas
            $pejabat = null;
            if (!empty($data['create_pejabat'])) {
                $pejabatData = [
                    'nama'              => $data['pejabat_nama'],
                    'jabatan'           => $data['pejabat_jabatan'],
                    'tugas'             => $data['pejabat_tugas'] ?? null,
                    'is_aktif'          => 1,
                    'is_petugas_barang' => !empty($data['is_petugas_barang']) ? 1 : 0,
                    'alamat'            => $data['pejabat_alamat'] ?? $data['alamat'] ?? null,
                    'ttd_path'          => $uploadedTtdPath,
                    'ttd_type'          => $uploadedTtdPath ? $request->input('ttd_type', 'gambar') : 'gambar',
                ];

                $pejabat = $this->pejabatService->createWithoutUpload($pejabatData);
                
                // Link user to pejabat
                $user->pejabat_id = $pejabat->id;
                $user->save();
            }
            
            DB::commit();

            ActivityLogger::log('User', 'tambah', "Menambahkan user {$user->nama} ({$user->username}) via API", $user->id, $user->nama);

            Log::info('API: user created successfully', [
                'user_id' => $user->id,
                'pejabat_id' => $pejabat?->id
            ]);

            return response()->json([
                'success' => true,
                'message' => !empty($data['create_pejabat'])
                    ? 'User dan Pejabat berhasil ditambahkan'
                    : 'User berhasil ditambahkan',
                'data' => [
                    'user' => $user->load(['pejabat', 'role:id,nama']),
                    'pejabat' => $pejabat
                ]
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file TTD yang sudah terupload karena DB-nya di-rollback
            if ($uploadedTtdPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($uploadedTtdPath);
            }

            Log::error('API: failed to create user', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menambahkan user.'
            ], 500);
        }
    }

    /**
     * Display the specified user
     */
    public function show($id)
    {
        $user = User::with(['pejabat', 'role:id,nama'])->findOrFail($id);
        if ($user->isSuperDeveloper()) {
            abort(404, 'User not found');
        }
        $this->authorize('view', $user);

        // Remove password
        unset($user->password);

        return response()->json([
            'success' => true,
            'data' => $user
        ]);
    }

    /**
     * Update the specified user
     */
    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::with('pejabat')->findOrFail($id);
        if ($user->isSuperDeveloper()) {
            abort(404, 'User not found');
        }
        $this->authorize('update', $user);
        $data = $request->validated();

        if (!empty($data['role']) && $data['role'] === 'super_developer') {
            return response()->json(['success' => false, 'message' => 'Role Super Developer tidak dapat dipilih.'], 422);
        }

        // Validasi: super_admin hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'super_admin') {
            if (User::excludeSuperDevelopers()->where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'super_admin'))->exists()) {
                return response()->json(['success' => false, 'message' => 'Sudah ada Super Admin. Hanya 1 Super Admin yang diperbolehkan.'], 422);
            }
        }
        // Validasi: perbekel hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'perbekel') {
            if (User::excludeSuperDevelopers()->where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->exists()) {
                return response()->json(['success' => false, 'message' => 'Sudah ada user dengan role Perbekel. Hanya 1 user yang diperbolehkan.'], 422);
            }
        }
        // Validasi: sekretaris desa hanya boleh 1 user
        if (!empty($data['role']) && stripos((string) $data['role'], 'sekretaris') !== false) {
            if (User::excludeSuperDevelopers()->where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists()) {
                return response()->json(['success' => false, 'message' => 'Sudah ada user dengan role Sekretaris Desa. Hanya 1 user yang diperbolehkan.'], 422);
            }
        }

        // Upload TTD di luar transaction agar bisa dihapus saat rollback
        $uploadedTtdPath = null;
        if ($request->hasFile('ttd_path')) {
            try {
                $uploadedTtdPath = $request->file('ttd_path')->store('ttd', 'public');
            } catch (\Throwable $e) {
                return response()->json(['success' => false, 'message' => 'Gagal mengupload file tanda tangan.'], 500);
            }
        }

        try {
            DB::beginTransaction();

            $roleModel = null;

            $user->nama = $data['nama'];
            $user->username = $data['username'];
            $user->alamat = $data['alamat'] ?? null;
            // $user->email = $data['email'];

            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
                app(\App\Services\Auth\DeviceSessionLimiter::class)->releaseAll($user);
            }

            if (array_key_exists('is_aktif', $data)) {
                $user->is_aktif = $data['is_aktif'];
                if (!$user->is_aktif) {
                    app(\App\Services\Auth\DeviceSessionLimiter::class)->releaseAll($user);
                }
            }

            if (!empty($data['role'])) {
                $roleModel = Role::where('nama', $data['role'])->first();
                if ($roleModel) {
                    $user->role_id = $roleModel->id;
                }
            }

            // handle bidang_id & dinas_id
            $currentRoleName = !empty($data['role']) ? $data['role'] : ($user->role?->nama ?? '');
            if (stripos($currentRoleName, 'kaur') !== false || stripos($currentRoleName, 'kasi') !== false || stripos($currentRoleName, 'staf') !== false) {
                if (array_key_exists('bidang_id', $data)) {
                    $user->bidang_id = $data['bidang_id'];
                }
                $user->dinas_id = null;
            } elseif (stripos($currentRoleName, 'kelian_banjar') !== false) {
                if (array_key_exists('dinas_id', $data)) {
                    $user->dinas_id = $data['dinas_id'];
                }
                $user->bidang_id = null;
            } else {
                if (array_key_exists('bidang_id', $data)) {
                    $user->bidang_id = $data['bidang_id'];
                }
                if (array_key_exists('dinas_id', $data)) {
                    $user->dinas_id = $data['dinas_id'];
                }
            }

            // Update pejabat if exists and user is linked
            if ($user->pejabat) {
                $pejabatData = [
                    'alamat' => $user->alamat,
                ];

                if (!empty($data['pejabat_nama'])) {
                    $pejabatData['nama'] = $data['pejabat_nama'];
                }
                if (!empty($data['pejabat_jabatan'])) {
                    $pejabatData['jabatan'] = $data['pejabat_jabatan'];
                }
                if (array_key_exists('pejabat_tugas', $data)) {
                    $pejabatData['tugas'] = $data['pejabat_tugas'];
                }
                if (array_key_exists('is_petugas_barang', $data)) {
                    $pejabatData['is_petugas_barang'] = !empty($data['is_petugas_barang']) ? 1 : 0;
                }

                // TTD sudah di-upload di atas, inject path langsung
                if ($uploadedTtdPath) {
                    $pejabatData['ttd_path']        = $uploadedTtdPath;
                    $pejabatData['ttd_type']        = $request->input('ttd_type', 'gambar');
                    $pejabatData['_ttd_pre_upload'] = true;
                }

                $this->pejabatService->updateWithPreUpload($user->pejabat->id, $pejabatData);
            }
            // Create new pejabat if requested but user does not have pejabat yet
            elseif (!empty($data['create_pejabat']) && empty($user->pejabat_id)) {
                $pejabatData = [
                    'nama'              => $data['pejabat_nama'] ?? $user->nama,
                    'jabatan'           => $data['pejabat_jabatan'] ?? ($roleModel?->nama ?? $user->role?->nama ?? 'Staff'),
                    'tugas'             => $data['pejabat_tugas'] ?? null,
                    'is_aktif'          => 1,
                    'is_petugas_barang' => !empty($data['is_petugas_barang']) ? 1 : 0,
                    'alamat'            => $data['pejabat_alamat'] ?? $user->alamat,
                    'ttd_path'          => $uploadedTtdPath,
                    'ttd_type'          => $uploadedTtdPath ? $request->input('ttd_type', 'gambar') : 'gambar',
                ];

                $p = $this->pejabatService->createWithoutUpload($pejabatData);
                if ($p) {
                    $user->pejabat_id = $p->id;
                }
            }

            $user->save();
            DB::commit();

            ActivityLogger::log('User', 'ubah', "Mengubah user {$user->nama} ({$user->username}) via API", $user->id, $user->nama);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil diupdate',
                'data' => $user->fresh()->load(['pejabat', 'role:id,nama'])
            ]);

        } catch (\Exception $e) {
            DB::rollBack();

            // Hapus file TTD baru yang sudah terupload karena DB-nya di-rollback
            if ($uploadedTtdPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($uploadedTtdPath);
            }

            Log::error('API: failed to update user', [
                'id' => $id,
                'error' => $e->getMessage()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal mengupdate user.'
            ], 500);
        }
    }

    /**
     * Remove the specified user
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        // Guard: Super Developer dan Super Admin tidak dapat dihapus oleh siapapun
        if ($user->isSuperDeveloper() || $user->isSuperAdmin()) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Super Developer atau Super Admin tidak dapat dihapus dari sistem.'
            ], 403);
        }

        $this->authorize('delete', $user);

        // Prevent self-deletion
        if ($user->id === auth()->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak dapat menghapus akun sendiri'
            ], 403);
        }

        // Blokir: user masih terhubung dengan pejabat, harus hapus pejabat dulu
        if ($user->pejabat_id) {
            $pejabat = \App\Models\Pejabat::find($user->pejabat_id);
            $namaPejabat = $pejabat ? $pejabat->nama : 'ID #' . $user->pejabat_id;
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat menghapus user '{$user->nama}' karena masih terhubung dengan pejabat '{$namaPejabat}'. Hapus pejabat tersebut terlebih dahulu.",
                'pejabat_nama' => $namaPejabat,
                'pejabat_id'   => $user->pejabat_id,
            ], 422);
        }
        $inventarisCreatedCount = \App\Models\Inventaris::where('created_by', $id)->count();
        if ($inventarisCreatedCount > 0) {
            return response()->json([
                'success' => false,
                'message' => "Tidak dapat menghapus user '{$user->nama}' karena masih memiliki {$inventarisCreatedCount} inventaris yang dibuat. Hapus atau ubah creator inventaris tersebut terlebih dahulu."
            ], 422);
        }

        /* Log::debug('API: deleting user', [
            'id' => $id,
            'admin' => auth()->id()
        ]); */

        try {
            DB::beginTransaction();

            // Cascade update - Set updated_by ke NULL
            $inventarisUpdatedCount = \App\Models\Inventaris::where('updated_by', $id)->count();
            if ($inventarisUpdatedCount > 0) {
                \App\Models\Inventaris::where('updated_by', $id)->update(['updated_by' => null]);
            }
            
            // Cascade update - Set user_id di export_logs ke NULL
            $exportLogCount = \App\Models\ExportLog::where('user_id', $id)->count();
            if ($exportLogCount > 0) {
                \App\Models\ExportLog::where('user_id', $id)->update(['user_id' => null]);
            }

            // Cascade delete - Hapus notifikasi & API token milik user
            \App\Models\Notification::where('user_id', $id)->delete();
            $user->tokens()->delete();

            $user->delete();
            
            DB::commit();

            ActivityLogger::log('User', 'hapus', "Menghapus user {$user->nama} ({$user->username}) via API", (int) $id, $user->nama);

            Log::info('API: User deleted successfully', [
                'user_id' => $id,
                'nama' => $user->nama,
                'inventaris_updated_nullified' => $inventarisUpdatedCount,
                'export_logs_nullified' => $exportLogCount,
                'deleted_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('API: failed to delete user', [
                'id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user.'
            ], 500);
        }
    }
}
