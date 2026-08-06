<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use App\Models\Role;
use App\Services\PejabatService;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    protected $pejabatService;

    public function __construct(PejabatService $pejabatService)
    {
        $this->pejabatService = $pejabatService;
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);
        
        // Build query with filters
        $query = User::with(['role', 'bidang', 'dinas', 'pejabat']);
        
        // Search filter
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                  ->orWhere('username', 'like', '%' . $search . '%');
                  // ->orWhere('email', 'like', '%' . $search . '%');
            });
        }
        
        // Role filter
        if ($request->has('roles') && is_array($request->roles) && count($request->roles) > 0) {
            $roles = $request->roles;
            $query->where(function($q) use ($roles) {
                foreach ($roles as $role) {
                    if ($role === 'Super Admin') {
                        $q->orWhere('role_id', 1);
                    } elseif ($role === 'Admin / SekDes') {
                        $q->orWhereHas('role', function($rq) {
                            $rq->where('nama', 'like', '%sekretaris%');
                        });
                    } elseif ($role === 'Admin / Kaur') {
                        $q->orWhereHas('role', function($rq) {
                            $rq->where('nama', 'like', '%kaur%');
                        });
                    } elseif ($role === 'Perbekel') {
                        $q->orWhereHas('role', function($rq) {
                            $rq->where('nama', 'like', '%perbekel%');
                        });
                    }
                }
            });
        }
        
        // Status filter
        if ($request->has('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->where('is_aktif', 1);
            } elseif ($request->status === 'inactive') {
                $query->where('is_aktif', 0);
            }
        }
        
        // Sort order
        $sortBy = $request->get('sort', 'nama_asc');
        if ($sortBy === 'nama_desc') {
            $query->orderBy('nama', 'desc');
        } elseif ($sortBy === 'terbaru') {
            $query->orderBy('created_at', 'desc');
        } elseif ($sortBy === 'terlama') {
            $query->orderBy('created_at', 'asc');
        } else {
            $query->orderBy('nama', 'asc');
        }

        // Paginate with dynamic per_page (options: 10, 20, 50, 100)
        $perPage = (int) $request->input('per_page', $request->input('limit', 10));
        if (!in_array($perPage, [10, 20, 50, 100], true)) {
            $perPage = 10;
        }

        /** @var \Illuminate\Pagination\LengthAwarePaginator $usersPaginated */
        $usersPaginated = $query->paginate($perPage)->appends($request->except('page'));
        
        // Get total counts from all users (not just current page)
        // Count stats: treat Admin group as roles matching sekretaris, kaur, perbekel, kasi, kelian_banjar, staf
        // (avoid hard-coding numeric role ids; use role.name patterns for consistency)
        $statsRow = User::query()
            ->leftJoin('roles as r', 'users.role_id', '=', 'r.id')
            ->selectRaw('COUNT(*) as total_users')
            ->selectRaw('SUM(CASE WHEN is_aktif = 1 THEN 1 ELSE 0 END) as user_aktif')
            ->selectRaw('SUM(CASE WHEN is_aktif = 0 THEN 1 ELSE 0 END) as user_tidak_aktif')
            ->selectRaw("SUM(CASE WHEN (LOWER(COALESCE(r.nama, '')) LIKE '%sekretaris%' OR LOWER(COALESCE(r.nama, '')) LIKE '%kaur%' OR LOWER(COALESCE(r.nama, '')) LIKE '%perbekel%' OR LOWER(COALESCE(r.nama, '')) LIKE '%kasi%' OR LOWER(COALESCE(r.nama, '')) LIKE '%kelian_banjar%' OR LOWER(COALESCE(r.nama, '')) LIKE '%staf%') THEN 1 ELSE 0 END) as total_admin")
            ->first();

        $stats = [
            'totalUsers' => (int) ($statsRow->total_users ?? 0),
            'userAktif' => (int) ($statsRow->user_aktif ?? 0),
            'userTidakAktif' => (int) ($statsRow->user_tidak_aktif ?? 0),
            'totalAdmin' => (int) ($statsRow->total_admin ?? 0), // Admin: sekretaris + kaur + perbekel + kasi + kelian_banjar + staf (EXCLUDE super_admin)
        ];
        
        // Avatar colors for cycling
        $avatarColors = [
            'linear-gradient(135deg, #6366f1 0%, #4f46e5 100%)',
            'linear-gradient(135deg, #10b981 0%, #059669 100%)',
            'linear-gradient(135deg, #f59e0b 0%, #d97706 100%)',
            'linear-gradient(135deg, #ec4899 0%, #db2777 100%)',
            'linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%)',
            'linear-gradient(135deg, #14b8a6 0%, #0d9488 100%)',
            'linear-gradient(135deg, #f43f5e 0%, #e11d48 100%)',
        ];
        
        // Determine role info from role name (use DB role `nama` field)
        // Avoid hard-coded numeric id mappings which may differ in DB

        // Transform paginated users with computed properties
        $usersPaginated->through(function($user, $index) use ($avatarColors) {
            // Generate initials
            $nama = $user->nama;
            $firstLetter = strtoupper(substr($nama, 0, 1));
            $secondLetter = strpos($nama, ' ') 
                ? strtoupper(substr($nama, strpos($nama, ' ') + 1, 1))
                : strtoupper(substr($nama, 1, 1));
            
            // Transform nama: remove underscores and capitalize
            $namaDisplay = ucwords(str_replace('_', ' ', $user->nama));
            
            $roleNameDisplay = ucwords(str_replace('_', ' ', ($user->role->nama ?? 'User')));
            $roleInfo = $this->buildRoleInfo($user);
            
            $user->nama_display = $namaDisplay;
            $user->role_name = $roleNameDisplay;
            $user->role_name_display = $roleInfo['name'] ?? $roleNameDisplay;
            $user->initials = $firstLetter . $secondLetter;
            $user->avatar_color = $avatarColors[$index % count($avatarColors)];
            $user->role_info = $roleInfo;
            
            return $user;
        });
        
        // If AJAX request, return JSON
        if ($request->ajax()) {
            /** @var \App\Models\User|null $authUser */
            $authUser = auth()->user();

            return response()->json([
                'users' => $usersPaginated->items(),
                'stats' => $stats,
                'pagination' => [
                    'firstItem' => $usersPaginated->firstItem(),
                    'lastItem' => $usersPaginated->lastItem(),
                    'currentPage' => $usersPaginated->currentPage(),
                    'lastPage' => $usersPaginated->lastPage(),
                    'perPage' => $usersPaginated->perPage(),
                    'total' => $usersPaginated->total(),
                ],
                'auth' => [
                    'isSuperAdmin' => (bool) ($authUser?->isSuperAdmin()),
                    'currentUserId' => auth()->id(),
                ],
            ]);
        }
        
        return view('users.index', [
            'users' => $usersPaginated,
            'stats' => $stats,
        ]);
    }

    public function create()
    {
        $this->authorize('create', User::class);
        $roles = Role::orderBy('nama')->get();
        $bidangs = \App\Models\Bidang::orderBy('nama')->get();
        $dinas = \App\Models\Dinas::orderBy('nama')->get();
        $perbekelExists = User::whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->exists();
        $sekretarisExists = User::whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists();
        $superAdminExists = User::where('role_id', 1)->exists();
        return view('users.create', [
            'roles' => $roles,
            'bidangs' => $bidangs,
            'dinas' => $dinas,
            'perbekelExists' => $perbekelExists,
            'sekretarisExists' => $sekretarisExists,
            'superAdminExists' => $superAdminExists,
        ]);
    }

    public function show($id)
    {
        $user = User::with(['role', 'pejabat', 'bidang', 'dinas'])->find($id);
        if (!$user) return redirect()->route('users.index')->withErrors('User tidak ditemukan');
        $this->authorize('view', $user);

        // Attach to user so view can reuse same mapping as index
        $user->role_info = $this->buildRoleInfo($user);

        return view('users.show', ['user' => $user]);
    }

    private function buildRoleInfo(User $user): array
    {
        $roleNamaRaw = $user->role->nama ?? '';
        $roleName = strtolower($roleNamaRaw);
        $roleNameDisplay = ucwords(str_replace('_', ' ', $roleNamaRaw ?: 'User'));

        if ($roleName === 'super_admin' || stripos($roleName, 'super_admin') !== false) {
            return ['name' => 'Super Admin', 'class' => 'super-admin', 'icon' => 'shield'];
        }

        if (stripos($roleName, 'sekretaris') !== false) {
            return ['name' => 'Sekretaris Desa', 'class' => 'admin', 'icon' => 'shield'];
        }

        if (stripos($roleName, 'kaur') !== false) {
            $bidangNama = trim((string) optional($user->bidang)->nama);
            $kaurName = $bidangNama !== '' ? ('Kaur - ' . $bidangNama) : 'Kaur';
            return ['name' => $kaurName, 'class' => 'admin', 'icon' => 'shield'];
        }

        if (stripos($roleName, 'kasi') !== false) {
            $bidangNama = trim((string) optional($user->bidang)->nama);
            $kasiName = $bidangNama !== '' ? ('Kasi - ' . $bidangNama) : 'Kasi';
            return ['name' => $kasiName, 'class' => 'admin', 'icon' => 'briefcase'];
        }

        if (stripos($roleName, 'kelian_banjar') !== false) {
            $dinasNama = trim((string) optional($user->dinas)->nama);
            $kelianName = $dinasNama !== '' ? ('Kelian Banjar Dinas ' . $dinasNama) : 'Kelian Banjar';
            return ['name' => $kelianName, 'class' => 'kelian', 'icon' => 'users'];
        }

        if (stripos($roleName, 'staf') !== false) {
            $bidangNama = trim((string) optional($user->bidang)->nama);
            $stafName = $bidangNama !== '' ? ('Staf - ' . $bidangNama) : 'Staf';
            return ['name' => $stafName, 'class' => 'staf', 'icon' => 'user-check'];
        }

        if (stripos($roleName, 'perbekel') !== false) {
            return ['name' => 'Perbekel', 'class' => 'perbekel', 'icon' => 'shield'];
        }

        return ['name' => $roleNameDisplay, 'class' => 'viewer', 'icon' => 'user'];
    }

    public function checkUsername(Request $request)
    {
        $this->authorize('create', User::class);

        $username = trim((string) $request->query('username', ''));
        $ignoreId = (int) $request->query('ignore_id', 0);

        if ($username === '') {
            return response()->json(['exists' => false]);
        }

        $query = User::query()->where('username', $username);
        if ($ignoreId > 0) {
            $query->where('id', '!=', $ignoreId);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function store(StoreUserRequest $request)
    {
        $this->authorize('create', User::class);
        $data = $request->validated();

        // Validasi: super_admin hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'super_admin') {
            if (User::where('role_id', 1)->exists()) {
                return redirect()->back()->withInput()->with('error', 'Sudah ada Super Admin. Hanya 1 Super Admin yang diperbolehkan.');
            }
        }

        // Validasi: perbekel hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'perbekel') {
            $alreadyExists = User::whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->exists();
            if ($alreadyExists) {
                return redirect()->back()->withInput()->with('error', 'Sudah ada user dengan role Perbekel. Hanya 1 user yang diperbolehkan.');
            }
        }

        // Validasi: sekretaris desa hanya boleh 1 user
        if (!empty($data['role']) && stripos((string) $data['role'], 'sekretaris') !== false) {
            $alreadyExists = User::whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists();
            if ($alreadyExists) {
                return redirect()->back()->withInput()->with('error', 'Sudah ada user dengan role Sekretaris Desa. Hanya 1 user yang diperbolehkan.');
            }
        }

        // Upload TTD di luar transaction agar path tersedia saat insert DB.
        // Kalau DB gagal, file akan dihapus di catch block.
        $uploadedTtdPath = null;
        if (!empty($data['create_pejabat']) && $request->hasFile('ttd_path')) {
            try {
                $uploadedTtdPath = $request->file('ttd_path')->store('ttd', 'public');
            } catch (\Throwable $e) {
                Log::error('UserController::store TTD upload error', ['error' => $e->getMessage()]);
                return redirect()->back()->withInput()->with('error', 'Gagal mengupload file tanda tangan. Silakan coba lagi.');
            }
        }

        try {
            // prepare user payload
            $userPayload = [
                'nama' => $data['nama'],
                'username' => $data['username'],
                // 'email' => $data['email'] ?? null,
                'password' => Hash::make($data['password']),
                'is_aktif' => $data['is_aktif'] ?? 1,
                'alamat' => $data['alamat'] ?? null,
            ];

            // resolve role -> role_id if Role model exists
            if (!empty($data['role'])) {
                $roleModel = Role::where('nama', $data['role'])->first();
                if ($roleModel) {
                    $userPayload['role_id'] = $roleModel->id;
                } else {
                    // store role as plain attribute if no role model match
                    $userPayload['role'] = $data['role'];
                }
            }

            // handle bidang_id if role is kaur, kasi, or staf
            if (!empty($data['role']) && (stripos($data['role'], 'kaur') !== false || stripos($data['role'], 'kasi') !== false || stripos($data['role'], 'staf') !== false)) {
                if (!empty($data['bidang_id'])) {
                    $userPayload['bidang_id'] = $data['bidang_id'];
                }
            }

            // handle dinas_id if role is kelian_banjar
            if (!empty($data['role']) && stripos($data['role'], 'kelian_banjar') !== false) {
                if (!empty($data['dinas_id'])) {
                    $userPayload['dinas_id'] = $data['dinas_id'];
                }
            }

            // Bungkus create pejabat + user dalam satu transaction.
            // Jika salah satu gagal, keduanya di-rollback — tidak ada pejabat yatim
            // yang tersimpan tanpa user, maupun user tanpa pejabat.
            \DB::beginTransaction();

            // optionally create Pejabat entry for this user
            if (!empty($data['create_pejabat'])) {
                $pejabatData = [
                    'nama'              => $data['pejabat_nama'],
                    'jabatan'           => $data['pejabat_jabatan'],
                    'tugas'             => $data['pejabat_tugas'] ?? null,
                    'is_aktif'          => 1,
                    'is_petugas_barang' => !empty($data['is_petugas_barang']) ? 1 : 0,
                    'alamat'            => $data['pejabat_alamat'] ?? $data['alamat'] ?? null,
                    // TTD sudah diupload sebelum transaction, inject path-nya langsung
                    'ttd_path'          => $uploadedTtdPath,
                    'ttd_type'          => $uploadedTtdPath ? $request->input('ttd_type', 'gambar') : 'gambar',
                ];

                // Lewati upload di service karena sudah ditangani di atas
                $p = $this->pejabatService->createWithoutUpload($pejabatData);
                if ($p) {
                    $userPayload['pejabat_id'] = $p->id;
                }
            }

            $user = User::create($userPayload);

            \DB::commit();

            ActivityLogger::log('User', 'tambah', 'Menambahkan user ' . $user->nama, $user->id, $user->nama);

            return redirect()->route('users.index')->with('success', 'Data user berhasil ditambahkan ke sistem!');
        } catch (\Throwable $e) {
            \DB::rollBack();

            // Hapus file TTD yang sudah terupload karena DB-nya di-rollback
            if ($uploadedTtdPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($uploadedTtdPath);
            }

            Log::error('UserController::store error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal menambahkan user. Silakan coba lagi.');
        }
    }

    public function edit($id)
    {
        $user = User::with('pejabat')->find($id);
        if (!$user) return redirect()->route('users.index')->withErrors('User tidak ditemukan');
        $this->authorize('update', $user);
        $roles = Role::orderBy('nama')->get();
        $bidangs = \App\Models\Bidang::orderBy('nama')->get();
        $dinas = \App\Models\Dinas::orderBy('nama')->get();
        $perbekelExists = User::where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->exists();
        $sekretarisExists = User::where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists();
        $superAdminExists = User::where('id', '!=', $id)->where('role_id', 1)->exists();
        return view('users.edit', [
            'user' => $user,
            'roles' => $roles,
            'bidangs' => $bidangs,
            'dinas' => $dinas,
            'perbekelExists' => $perbekelExists,
            'sekretarisExists' => $sekretarisExists,
            'superAdminExists' => $superAdminExists,
        ]);
    }

    public function update(UpdateUserRequest $request, $id)
    {
        $user = User::with('pejabat')->find($id);
        if (!$user) return redirect()->route('users.index')->withErrors('User tidak ditemukan');
        $this->authorize('update', $user);

        $data = $request->validated();

        // Validasi: super_admin hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'super_admin') {
            if (User::where('id', '!=', $id)->where('role_id', 1)->exists()) {
                return redirect()->back()->withInput()->with('error', 'Sudah ada Super Admin. Hanya 1 Super Admin yang diperbolehkan.');
            }
        }

        // Validasi: perbekel hanya boleh 1 user
        if (!empty($data['role']) && $data['role'] === 'perbekel') {
            $alreadyExists = User::where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'perbekel'))->exists();
            if ($alreadyExists) {
                return redirect()->back()->withInput()->with('error', 'Sudah ada user dengan role Perbekel. Hanya 1 user yang diperbolehkan.');
            }
        }

        // Validasi: sekretaris desa hanya boleh 1 user
        if (!empty($data['role']) && stripos((string) $data['role'], 'sekretaris') !== false) {
            $alreadyExists = User::where('id', '!=', $id)->whereHas('role', fn($q) => $q->where('nama', 'like', '%sekretaris%'))->exists();
            if ($alreadyExists) {
                return redirect()->back()->withInput()->with('error', 'Sudah ada user dengan role Sekretaris Desa. Hanya 1 user yang diperbolehkan.');
            }
        }

        // Upload TTD di luar transaction agar path tersedia saat insert DB.
        // Kalau DB gagal, file akan dihapus di catch block.
        $uploadedTtdPath = null;
        $needsTtdUpload = $request->hasFile('ttd_path');
        if ($needsTtdUpload) {
            try {
                $uploadedTtdPath = $request->file('ttd_path')->store('ttd', 'public');
            } catch (\Throwable $e) {
                Log::error('UserController::update TTD upload error', ['error' => $e->getMessage()]);
                return redirect()->back()->withInput()->with('error', 'Gagal mengupload file tanda tangan. Silakan coba lagi.');
            }
        }

        try {
            // Bungkus seluruh operasi update user + pejabat dalam satu transaction.
            // Jika salah satu gagal, keduanya di-rollback.
            \DB::beginTransaction();

            // Update user data
            $user->nama = $data['nama'];
            $user->username = $data['username'];
            $user->alamat = $data['alamat'] ?? null;
            // $user->email = $data['email'];
            if (!empty($data['password'])) {
                $user->password = Hash::make($data['password']);
            }
            if (array_key_exists('is_aktif', $data)) {
                $user->is_aktif = $data['is_aktif'];
            }

            $roleModel = null;
            if (!empty($data['role'])) {
                $roleModel = Role::where('nama', $data['role'])->first();
                if ($roleModel) {
                    $user->role_id = $roleModel->id;
                }
            }

            // handle bidang_id if role is kaur, kasi, or staf
            if (!empty($data['role']) && (stripos($data['role'], 'kaur') !== false || stripos($data['role'], 'kasi') !== false || stripos($data['role'], 'staf') !== false)) {
                if (!empty($data['bidang_id'])) {
                    $user->bidang_id = $data['bidang_id'];
                } else {
                    $user->bidang_id = null;
                }
            } else {
                // jika bukan kaur/kasi/staf, clear bidang_id
                $user->bidang_id = null;
            }

            // handle dinas_id if role is kelian_banjar
            if (!empty($data['role']) && stripos($data['role'], 'kelian_banjar') !== false) {
                if (!empty($data['dinas_id'])) {
                    $user->dinas_id = $data['dinas_id'];
                } else {
                    $user->dinas_id = null;
                }
            } else {
                // jika bukan kelian_banjar, clear dinas_id
                $user->dinas_id = null;
            }

            // If user has pejabat, update pejabat data
            if ($user->pejabat) {
                $roleNama = $roleModel?->nama ?? (string) ($data['role'] ?? '');
                $bidangNama = null;
                $dinasNama = null;
                if (!empty($user->bidang_id)) {
                    $bidangNama = optional(\App\Models\Bidang::find($user->bidang_id))->nama;
                }
                if (!empty($user->dinas_id)) {
                    $dinasNama = optional(\App\Models\Dinas::find($user->dinas_id))->nama;
                }

                $pejabatUpdateData = [
                    'nama'    => $user->nama,
                    'jabatan' => $this->resolveJabatanFromUserRole($roleNama, $bidangNama, $dinasNama),
                    'alamat'  => $user->alamat,
                ];

                if (isset($data['pejabat_tugas'])) {
                    $pejabatUpdateData['tugas'] = $data['pejabat_tugas'];
                }
                // TTD sudah diupload sebelum transaction, inject path-nya langsung
                if ($uploadedTtdPath) {
                    $pejabatUpdateData['ttd_path']        = $uploadedTtdPath;
                    $pejabatUpdateData['ttd_type']        = $request->input('ttd_type', 'gambar');
                    $pejabatUpdateData['_ttd_pre_upload'] = true; // flag: skip upload di service
                }
                // Propagate is_petugas_barang jika ada di request
                if (array_key_exists('is_petugas_barang', $data)) {
                    $pejabatUpdateData['is_petugas_barang'] = !empty($data['is_petugas_barang']) ? 1 : 0;
                }

                $this->pejabatService->updateWithPreUpload($user->pejabat->id, $pejabatUpdateData);
            }
            // Create new pejabat if checkbox checked but no pejabat yet
            elseif (!empty($data['create_pejabat']) && empty($user->pejabat_id)) {
                $pejabatData = [
                    'nama'              => $data['pejabat_nama'] ?? $user->nama,
                    'jabatan'           => $data['pejabat_jabatan'] ?? ($roleModel->nama ?? 'Staff'),
                    'tugas'             => $data['pejabat_tugas'] ?? null,
                    'is_aktif'          => 1,
                    'is_petugas_barang' => !empty($data['is_petugas_barang']) ? 1 : 0,
                    'alamat'            => $data['pejabat_alamat'] ?? $user->alamat,
                    // TTD sudah diupload sebelum transaction, inject path-nya langsung
                    'ttd_path' => $uploadedTtdPath,
                    'ttd_type' => $uploadedTtdPath ? $request->input('ttd_type', 'gambar') : 'gambar',
                ];

                $p = $this->pejabatService->createWithoutUpload($pejabatData);
                if ($p) {
                    $user->pejabat_id = $p->id;
                }
            }

            $user->save();

            \DB::commit();

            // Hapus TTD lama jika ada upload baru dan pejabat sudah ada (dilakukan setelah commit)
            // Ini best-effort: file lama dihapus setelah DB berhasil di-commit
            if ($uploadedTtdPath && $user->pejabat && isset($pejabatUpdateData)) {
                // TTD lama sudah di-handle di service (updateWithPreUpload)
            }

            ActivityLogger::log('User', 'ubah', 'Mengubah data user ' . $user->nama, $user->id, $user->nama);

            return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
        } catch (\Throwable $e) {
            \DB::rollBack();

            // Hapus file TTD baru yang sudah terupload karena DB-nya di-rollback
            if ($uploadedTtdPath) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($uploadedTtdPath);
            }

            Log::error('UserController::update error', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui user. Silakan coba lagi.');
        }
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if (!$user) return redirect()->route('users.index')->withErrors('User tidak ditemukan');
        $this->authorize('delete', $user);

        // Guard: Super Admin tidak dapat dihapus oleh siapapun
        if ($user->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Super Admin tidak dapat dihapus dari sistem.');
        }

        // Blokir: user masih terhubung dengan pejabat, harus hapus pejabat dulu
        if ($user->pejabat_id) {
            $pejabat = \App\Models\Pejabat::find($user->pejabat_id);
            $namaPejabat = $pejabat ? $pejabat->nama : 'ID #' . $user->pejabat_id;
            return redirect()->back()->with('error',
                "Tidak dapat menghapus user '{$user->nama}' karena masih terhubung dengan pejabat '{$namaPejabat}'. Hapus pejabat tersebut terlebih dahulu.");
        }
        $inventarisCreatedCount = \App\Models\Inventaris::where('created_by', $id)->count();
        if ($inventarisCreatedCount > 0) {
            return redirect()->back()->with('error', 
                "Tidak dapat menghapus user '{$user->nama}' karena masih memiliki {$inventarisCreatedCount} inventaris yang dibuat. Hapus atau ubah creator inventaris tersebut terlebih dahulu.");
        }

        // Cek relasi lain
        $inventarisUpdatedCount = \App\Models\Inventaris::where('updated_by', $id)->count();
        $exportLogCount = \App\Models\ExportLog::where('user_id', $id)->count();

        try {
            \DB::beginTransaction();
            
            // Cascade update - Set updated_by ke NULL
            if ($inventarisUpdatedCount > 0) {
                \App\Models\Inventaris::where('updated_by', $id)->update(['updated_by' => null]);
            }
            
            // Cascade update - Set user_id di export_logs ke NULL
            if ($exportLogCount > 0) {
                \App\Models\ExportLog::where('user_id', $id)->update(['user_id' => null]);
            }
            
            $user->delete();
            
            ActivityLogger::log('User', 'hapus', 'Menghapus user ' . $user->nama, (int) $id, $user->nama);
            \DB::commit();
            
            Log::info('User deleted successfully', [
                'user_id' => $id,
                'nama' => $user->nama,
                'inventaris_updated_nullified' => $inventarisUpdatedCount,
                'export_logs_nullified' => $exportLogCount,
                'deleted_by' => auth()->id(),
            ]);
            
            return redirect()->route('users.index')->with('success', 'Data user berhasil dihapus dari sistem!');
        } catch (\Throwable $e) {
            \DB::rollBack();
            Log::error('UserController::destroy error', [
                'user_id' => $id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus user. Silakan coba lagi.');
        }
    }

    public function bulkDestroy(Request $request)
    {
        $this->authorize('bulkDelete', User::class);

        $request->validate([
            'ids'   => ['required', 'array', 'min:1'],
            'ids.*' => ['integer'],
        ]);

        $ids = array_filter((array) $request->input('ids', []));
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Tidak ada item yang dipilih.');
        }
        $totalRequested = count($ids);
        $ids = array_slice($ids, 0, 100);

        $deleted = 0;
        $skipped = 0;
        \DB::beginTransaction();
        try {
            foreach ($ids as $id) {
                if ($id == auth()->id()) { $skipped++; continue; } // jangan hapus diri sendiri
                $user = User::find($id);
                if (!$user) continue;
                $this->authorize('delete', $user);

                // Guard: Super Admin tidak dapat dihapus
                if ($user->isSuperAdmin()) { $skipped++; continue; }

                // Blokir: user masih terhubung dengan pejabat
                if ($user->pejabat_id) { $skipped++; continue; }

                // Cek relasi inventaris
                $inventarisCreatedCount = \App\Models\Inventaris::where('created_by', $id)->count();
                if ($inventarisCreatedCount > 0) { $skipped++; continue; }

                \App\Models\Inventaris::where('updated_by', $id)->update(['updated_by' => null]);
                \App\Models\ExportLog::where('user_id', $id)->update(['user_id' => null]);
                $user->delete();
                $deleted++;
            }
            \DB::commit();
            $msg = $deleted . ' user berhasil dihapus.';
            if ($skipped > 0) $msg .= ' ' . $skipped . ' dilewati (akun sendiri atau masih memiliki data inventaris).';
            if ($totalRequested > 100) {
                $msg .= ' ' . ($totalRequested - 100) . ' data tersisa, pilih ulang untuk menghapus selanjutnya.';
            }
            return redirect()->route('users.index')->with('success', $msg);
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            \DB::rollBack();
            throw $e;
        } catch (\Exception $e) {
            \DB::rollBack();
            Log::error('UserController::bulkDestroy error', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

    public function toggleAktif($id)
    {
        $user = User::find($id);
        if (!$user) return redirect()->route('users.index')->withErrors('User tidak ditemukan');
        $this->authorize('update', $user);

        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Tidak dapat menonaktifkan akun Anda sendiri.');
        }

        $user->is_aktif = !$user->is_aktif;
        $user->save();

        $statusLabel = $user->is_aktif ? 'diaktifkan' : 'dinonaktifkan';
        ActivityLogger::log('User', 'ubah', "User {$user->nama} {$statusLabel}", $user->id, $user->nama);

        return redirect()->back()->with('success', "User '{$user->nama}' berhasil {$statusLabel}.");
    }

    private function resolveJabatanFromUserRole(string $roleNama, ?string $bidangNama = null, ?string $dinasNama = null): string
    {
        $role = strtolower(trim($roleNama));

        if (str_contains($role, 'kaur')) {
            return $bidangNama ? ('Kaur ' . trim($bidangNama)) : 'Kaur';
        }
        if (str_contains($role, 'kasi')) {
            return $bidangNama ? ('Kasi ' . trim($bidangNama)) : 'Kasi';
        }
        if (str_contains($role, 'kelian_banjar')) {
            return $dinasNama ? ('Kelian Banjar Dinas ' . trim($dinasNama)) : 'Kelian Banjar';
        }
        if (str_contains($role, 'staf')) {
            return $bidangNama ? ('Staf ' . trim($bidangNama)) : 'Staf';
        }
        if (str_contains($role, 'sekretaris')) {
            return 'Sekretaris Desa';
        }
        if (str_contains($role, 'perbekel')) {
            return 'Perbekel';
        }
        if (str_contains($role, 'super_admin')) {
            return 'Super Admin';
        }

        return $roleNama !== '' ? ucwords(str_replace('_', ' ', $roleNama)) : 'Staff';
    }
}
