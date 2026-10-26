<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Role;
use App\Models\Bidang;
use App\Models\Dinas;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class UserImport implements ToCollection, WithHeadingRow, WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            0 => $this,
        ];
    }
    public int $totalRows = 0;
    public int $importedCount = 0;
    public int $skippedCount = 0;
    public int $failedCount = 0;

    public array $importedRows = [];
    public array $skippedRows = [];
    public array $failedRows = [];

    protected array $seenInFile = [];
    protected Collection $existingUsers;
    protected Collection $roles;
    protected Collection $bidangs;
    protected Collection $dinasList;

    public function __construct()
    {
        $this->existingUsers = User::with('role')->get();
        $this->roles = Role::all();
        $this->bidangs = Bidang::all();
        $this->dinasList = Dinas::all();
    }

    public function collection(Collection $rows): void
    {
        // 1. Strict Header Verification (Mencegah Salah Berkas / Mismatched Template)
        $firstRow = $rows->first();
        if ($firstRow) {
            $firstRowArr = $firstRow instanceof Collection ? $firstRow->toArray() : (array)$firstRow;
            $hasUsernameHeader = false;
            $hasOtherModuleSignature = false;

            foreach (array_keys($firstRowArr) as $colKey) {
                $cleanCol = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$colKey));
                if (str_contains($cleanCol, 'username') || $cleanCol === 'akun') {
                    $hasUsernameHeader = true;
                }
                // Jika memiliki kolom khas modul lain (misal kodebarang / nup milik Inventaris, namabidang milik Bidang), tolak
                if (str_contains($cleanCol, 'kodebarang') || str_contains($cleanCol, 'nup') || str_contains($cleanCol, 'namabidang') || str_contains($cleanCol, 'namaruang')) {
                    $hasOtherModuleSignature = true;
                }
            }

            if (!$hasUsernameHeader || $hasOtherModuleSignature) {
                throw new \InvalidArgumentException('Format berkas tidak sesuai untuk modul Pengguna. Berkas yang diunggah tidak memiliki kolom "Username (Unik)". Pastikan Anda mengunggah Template Import Pengguna.');
            }
        }

        $rowIndex = 1;

        foreach ($rows as $row) {
            $rowIndex++;

            $rowArr = $row instanceof Collection ? $row->toArray() : (array)$row;

            $hasAnyValue = false;
            foreach ($rowArr as $val) {
                if ($val !== null && trim((string)$val) !== '') {
                    $hasAnyValue = true;
                    break;
                }
            }
            if (!$hasAnyValue) {
                continue;
            }

            // Abaikan baris komentar / petunjuk format CSV
            $firstVal = trim((string)($row instanceof Collection ? $row->first() : (array_values((array)$row)[0] ?? '')));
            if (str_starts_with($firstVal, '#') || str_starts_with($firstVal, '[') || str_contains(strtoupper($firstVal), 'PETUNJUK') || str_contains(strtoupper($firstVal), 'PANDUAN')) {
                continue;
            }

            $this->totalRows++;

            // Resolusi kolom dengan multi-alias dan fuzzy match nama kolom (Strict Header Matching)
            $nama = $this->getRowValue($rowArr, [
                'nama_lengkap', 'nama', 'nama_user', 'nama_staf', 'nama_lengkap_dan_gelar', 'nama_lengkap_'
            ]);

            $username = $this->getRowValue($rowArr, [
                'username_unik', 'username', 'user_name', 'username_unik_', 'user', 'akun'
            ]);

            $roleRaw = $this->getRowValue($rowArr, [
                'peran_role', 'role', 'peran', 'peran_role_', 'peran_atau_role'
            ]);

            $bidangRaw = $this->getRowValue($rowArr, [
                'bidang_khusus_kaur', 'bidang', 'nama_bidang', 'bidang_khusus_kaur_kasi', 'urusan'
            ]);

            $dinasRaw = $this->getRowValue($rowArr, [
                'banjar_dinas', 'dinas', 'nama_dinas', 'banjar', 'banjar_dinas_khusus_kelian'
            ]);

            $statusAktifRaw = $this->getRowValue($rowArr, [
                'status_akun_aktif_nonaktif', 'status_aktif', 'status_akun', 'status', 'is_aktif'
            ]);

            $alamat = $this->getRowValue($rowArr, [
                'alamat_domisili', 'alamat', 'alamat_lengkap', 'domisili'
            ]);

            $password = $this->getRowValue($rowArr, [
                'password_awal_opsional', 'password_awal', 'password', 'kata_sandi', 'pass'
            ]);

            // Jika format lama terdeteksi (kolom 4 password, kolom 5 status, kolom 6 alamat):
            // Lakukan deteksi cerdas jika statusAktif berisi kata sandi atau sebaliknya
            if ($password === null && !empty($statusAktifRaw) && strtolower($statusAktifRaw) !== 'aktif' && strtolower($statusAktifRaw) !== 'nonaktif') {
                $password = $statusAktifRaw;
                $statusAktifRaw = 'Aktif';
            }

            // Validasi wajib
            if (empty($nama) || empty($username) || empty($roleRaw)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama ?: '-',
                    'username' => $username ?: '-',
                    'reason' => 'Nama Lengkap, Username, dan Role wajib diisi',
                ];
                continue;
            }

            if (!preg_match('/[a-zA-Z]/', (string)$nama)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $username ?: '-',
                    'reason' => 'Nama Lengkap tidak valid (harus mengandung huruf, tidak boleh hanya angka atau simbol)',
                ];
                continue;
            }

            // Bersihkan username (hanya alfanumerik, titik, strip, underscore)
            $usernameClean = strtolower(trim(preg_replace('/[^a-zA-Z0-9._-]/', '', (string)$username)));
            if (strlen($usernameClean) < 3) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $username,
                    'reason' => 'Username harus minimal 3 karakter',
                ];
                continue;
            }

            // Cegah import username yang dicadangkan sistem untuk Super Developer
            $superDevUsernames = array_map('strtolower', \App\Models\User::getSuperDeveloperUsernames());
            if (in_array($usernameClean, $superDevUsernames, true)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $usernameClean,
                    'reason' => 'Username tersebut dicadangkan untuk sistem dan tidak dapat digunakan',
                ];
                continue;
            }

            // Cek duplikasi di dalam file yang sama
            if (isset($this->seenInFile[$usernameClean])) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $usernameClean,
                    'reason' => 'Duplikat dengan baris #' . $this->seenInFile[$usernameClean] . ' pada berkas Excel ini',
                ];
                continue;
            }
            $this->seenInFile[$usernameClean] = $rowIndex;

            // Cek duplikasi di database
            $isDuplicateInDb = $this->existingUsers->contains(function ($u) use ($usernameClean) {
                return strcasecmp($u->username, $usernameClean) === 0;
            });

            if ($isDuplicateInDb) {
                $this->skippedCount++;
                $this->skippedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $usernameClean,
                    'reason' => 'Username sudah terdaftar di sistem',
                ];
                continue;
            }

            // Resolusi role cerdas (cocokkan string case-insensitive & tanpa underscore/spasi)
            $cleanRoleInput = strtolower(str_replace([' ', '-', '_', '/'], '', (string)$roleRaw));
            $matchedRole = $this->roles->first(function ($r) use ($cleanRoleInput, $roleRaw) {
                $cleanDb = strtolower(str_replace([' ', '-', '_'], '', $r->nama));
                if ($cleanDb === $cleanRoleInput) return true;
                if (strcasecmp($r->nama, $roleRaw) === 0) return true;
                if ($cleanRoleInput === 'sekretaris' && $r->nama === 'sekretaris_desa') return true;
                if ($cleanRoleInput === 'kepaladesa' && $r->nama === 'perbekel') return true;
                if ($cleanRoleInput === 'admin' && $r->nama === 'super_admin') return true;
                return str_contains($cleanDb, $cleanRoleInput) || str_contains($cleanRoleInput, $cleanDb);
            });

            if (!$matchedRole) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $usernameClean,
                    'reason' => "Role '{$roleRaw}' tidak ditemukan di sistem",
                ];
                continue;
            }

            // Cegah import user dengan role Super Developer
            if (in_array(strtolower((string)$matchedRole->nama), ['super_developer', 'developer', 'owner'], true)) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row' => $rowIndex,
                    'nama' => $nama,
                    'username' => $usernameClean,
                    'reason' => 'Role Super Developer tidak dapat ditambahkan melalui impor',
                ];
                continue;
            }

            // Validasi singular role: super_admin, perbekel, dan sekretaris_desa hanya boleh 1 user di seluruh sistem
            $matchedRoleName = strtolower((string)$matchedRole->nama);
            if ($matchedRoleName === 'super_admin') {
                $hasSuperAdmin = $this->existingUsers->contains(function ($u) {
                    return !$u->isSuperDeveloper() && ($u->role_id == 1 || ($u->role && $u->role->nama === 'super_admin'));
                });
                if ($hasSuperAdmin) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'username' => $usernameClean,
                        'reason' => 'Sudah ada Super Admin di sistem. Hanya 1 Super Admin yang diperbolehkan.',
                    ];
                    continue;
                }
            }

            if ($matchedRoleName === 'perbekel') {
                $hasPerbekel = $this->existingUsers->contains(function ($u) use ($matchedRole) {
                    return ($u->role_id && $u->role_id == $matchedRole->id) || ($u->role && $u->role->nama === 'perbekel');
                });
                if ($hasPerbekel) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'username' => $usernameClean,
                        'reason' => 'Sudah ada user dengan role Perbekel. Hanya 1 user yang diperbolehkan.',
                    ];
                    continue;
                }
            }

            if (str_contains($matchedRoleName, 'sekretaris')) {
                $hasSekretaris = $this->existingUsers->contains(function ($u) use ($matchedRole) {
                    return ($u->role_id && $u->role_id == $matchedRole->id) || ($u->role && str_contains(strtolower($u->role->nama), 'sekretaris'));
                });
                if ($hasSekretaris) {
                    $this->failedCount++;
                    $this->failedRows[] = [
                        'row' => $rowIndex,
                        'nama' => $nama,
                        'username' => $usernameClean,
                        'reason' => 'Sudah ada user dengan role Sekretaris Desa. Hanya 1 user yang diperbolehkan.',
                    ];
                    continue;
                }
            }

            // Resolusi bidang jika diisi
            $bidangId = null;
            if (!empty($bidangRaw)) {
                $cleanBidangInput = strtolower(str_replace([' ', '-', '_'], '', (string)$bidangRaw));
                $matchedBidang = $this->bidangs->first(function ($b) use ($cleanBidangInput, $bidangRaw) {
                    $cleanDb = strtolower(str_replace([' ', '-', '_'], '', $b->nama));
                    if ($cleanDb === $cleanBidangInput) return true;
                    if (strcasecmp($b->nama, $bidangRaw) === 0) return true;
                    return str_contains($cleanDb, $cleanBidangInput) || str_contains($cleanBidangInput, $cleanDb);
                });
                if ($matchedBidang) {
                    $bidangId = $matchedBidang->id;
                }
            }

            // Resolusi dinas jika diisi
            $dinasId = null;
            if (!empty($dinasRaw)) {
                $cleanDinasInput = strtolower(str_replace([' ', '-', '_', 'banjar', 'dinas'], '', (string)$dinasRaw));
                $matchedDinas = $this->dinasList->first(function ($d) use ($cleanDinasInput, $dinasRaw) {
                    $cleanDb = strtolower(str_replace([' ', '-', '_', 'banjar', 'dinas'], '', $d->nama));
                    if ($cleanDb === $cleanDinasInput) return true;
                    if (strcasecmp($d->nama, $dinasRaw) === 0) return true;
                    return str_contains($cleanDb, $cleanDinasInput) || str_contains($cleanDinasInput, $cleanDb);
                });
                if ($matchedDinas) {
                    $dinasId = $matchedDinas->id;
                }
            }

            // Resolusi status aktif
            $isAktif = true;
            if (!empty($statusAktifRaw)) {
                $cleanStatus = strtolower(trim((string)$statusAktifRaw));
                $isAktif = !in_array($cleanStatus, ['tidak', 'nonaktif', '0', 'false', 'no', 't', 'non-aktif', 'pasif']);
            }

            $plainPassword = !empty($password) ? (string)$password : 'password123';

            try {
                $newUser = User::create([
                    'nama'      => $nama,
                    'username'  => $usernameClean,
                    'password'  => Hash::make($plainPassword),
                    'role_id'   => $matchedRole->id,
                    'bidang_id' => $bidangId,
                    'dinas_id'  => $dinasId,
                    'is_aktif'  => $isAktif,
                    'alamat'    => $alamat ?: null,
                ]);

                $newUser->setRelation('role', $matchedRole);
                $this->existingUsers->push($newUser);
                $this->importedCount++;
                $this->importedRows[] = [
                    'row'      => $rowIndex,
                    'id'       => $newUser->id,
                    'nama'     => $nama,
                    'username' => $usernameClean,
                ];
            } catch (\Throwable $e) {
                $this->failedCount++;
                $this->failedRows[] = [
                    'row'      => $rowIndex,
                    'nama'     => $nama,
                    'username' => $usernameClean,
                    'reason'   => 'Gagal menyimpan: ' . $e->getMessage(),
                ];
            }
        }
    }

    protected function getRowValue(array $arr, array $keys): ?string
    {
        // 1. Cek exact key
        foreach ($keys as $k) {
            if (array_key_exists($k, $arr) && $arr[$k] !== null) {
                $val = $this->cleanValue((string)$arr[$k]);
                if ($val !== null) return $val;
            }
        }

        // 2. Cek cleaned alphanumeric key match
        foreach ($arr as $k => $v) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$k));
            foreach ($keys as $target) {
                $cleanTarget = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$target));
                if ($cleanKey === $cleanTarget && $v !== null) {
                    $val = $this->cleanValue((string)$v);
                    if ($val !== null) return $val;
                }
            }
        }

        // 3. Cek contains substring key match
        foreach ($arr as $k => $v) {
            $cleanKey = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$k));
            foreach ($keys as $target) {
                $cleanTarget = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', (string)$target));
                if (strlen($cleanTarget) >= 4 && (str_contains($cleanKey, $cleanTarget) || str_contains($cleanTarget, $cleanKey)) && $v !== null) {
                    $val = $this->cleanValue((string)$v);
                    if ($val !== null) return $val;
                }
            }
        }

        return null;
    }

    /**
     * Bersihkan nilai sel dari UTF-8 BOM, null bytes, karakter kontrol tak terlihat, dan formula injection.
     */
    protected function cleanValue(?string $val): ?string
    {
        if ($val === null) {
            return null;
        }

        // Hapus UTF-8 BOM jika ada di awal string
        $val = preg_replace('/^\xEF\xBB\xBF/', '', $val);

        // Hapus null-byte
        $val = str_replace("\0", '', $val);

        // Hapus karakter kontrol non-printable tak terlihat
        $val = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/u', '', $val);

        $val = trim($val);

        // Netralkan potensi formula injection jika diawali '=' atau '@'
        if (str_starts_with($val, '=') || str_starts_with($val, '@')) {
            $val = ltrim($val, '=@');
            $val = trim($val);
        }

        return $val !== '' ? $val : null;
    }
}
