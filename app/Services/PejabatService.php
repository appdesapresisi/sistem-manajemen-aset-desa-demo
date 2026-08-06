<?php

namespace App\Services;

use App\Models\Pejabat;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class PejabatService
{
    public function getAll()
    {
        return Pejabat::orderBy('nama')->paginate(10);
    }

    public function getById($id)
    {
        return Pejabat::find($id);
    }

    public function create(array $data)
    {
        if (!empty($data['ttd_path'])) {
            $path = $this->storeTtd($data['ttd_path']);
            $data['ttd_path'] = $path;
        }

        // Jika pejabat ini dijadikan Petugas Barang, reset yang lain dulu
        if (!empty($data['is_petugas_barang'])) {
            Pejabat::where('is_petugas_barang', 1)->update(['is_petugas_barang' => 0]);
            $data['is_petugas_barang'] = 1;
        } else {
            $data['is_petugas_barang'] = 0;
        }

        return Pejabat::create($data);
    }

    /**
     * Buat pejabat tanpa upload TTD — TTD path sudah di-handle di luar (pre-upload).
     * Digunakan saat create dipanggil di dalam DB transaction agar rollback bisa
     * membersihkan file yang sudah terupload dari catch block di controller.
     */
    public function createWithoutUpload(array $data): Pejabat
    {
        // Jika pejabat ini dijadikan Petugas Barang, reset yang lain dulu
        if (!empty($data['is_petugas_barang'])) {
            Pejabat::where('is_petugas_barang', 1)->update(['is_petugas_barang' => 0]);
            $data['is_petugas_barang'] = 1;
        } else {
            $data['is_petugas_barang'] = 0;
        }

        return Pejabat::create($data);
    }

    public function update($id, array $data)
    {
        $pejabat = Pejabat::find($id);
        if (!$pejabat) return null;

        if (!empty($data['ttd_path']) && is_object($data['ttd_path'])) {
            $path = $this->storeTtd($data['ttd_path']);
            if ($pejabat->ttd_path && Storage::disk('public')->exists($pejabat->ttd_path)) {
                Storage::disk('public')->delete($pejabat->ttd_path);
            }
            $data['ttd_path'] = $path;
        }

        // Handle is_petugas_barang — hanya 1 yang boleh aktif
        // Checkbox HTML tidak mengirim value saat un-check, jadi default ke 0
        $data['is_petugas_barang'] = !empty($data['is_petugas_barang']) ? 1 : 0;
        if ($data['is_petugas_barang']) {
            Pejabat::where('is_petugas_barang', 1)
                ->where('id', '!=', $id)
                ->update(['is_petugas_barang' => 0]);
        }

        $oldNama = $pejabat->nama;
        $oldAlamat = $pejabat->alamat;
        $pejabat->update($data);

        if (array_key_exists('nama', $data) && trim((string) $data['nama']) !== '' && $oldNama !== $pejabat->nama) {
            \App\Models\User::where('pejabat_id', $pejabat->id)->update([
                'nama' => $pejabat->nama,
            ]);
        }

        if (array_key_exists('alamat', $data) && $oldAlamat !== $pejabat->alamat) {
            \App\Models\User::where('pejabat_id', $pejabat->id)->update([
                'alamat' => $pejabat->alamat,
            ]);
        }

        return $pejabat;
    }

    /**
     * Update pejabat dengan TTD path yang sudah di-pre-upload oleh controller.
     * Jika ada flag _ttd_pre_upload = true, path TTD sudah berupa string — skip upload,
     * tapi tetap hapus TTD lama dari disk.
     * Digunakan saat update dipanggil di dalam DB transaction.
     */
    public function updateWithPreUpload($id, array $data): ?Pejabat
    {
        $pejabat = Pejabat::find($id);
        if (!$pejabat) return null;

        // Jika ada TTD baru yang sudah pre-upload, hapus TTD lama dari disk
        if (!empty($data['_ttd_pre_upload']) && !empty($data['ttd_path'])) {
            if ($pejabat->ttd_path && Storage::disk('public')->exists($pejabat->ttd_path)) {
                Storage::disk('public')->delete($pejabat->ttd_path);
            }
        }
        // Hapus flag internal sebelum dipass ke model
        unset($data['_ttd_pre_upload']);

        // Handle is_petugas_barang — hanya 1 yang boleh aktif
        $data['is_petugas_barang'] = !empty($data['is_petugas_barang']) ? 1 : 0;
        if ($data['is_petugas_barang']) {
            Pejabat::where('is_petugas_barang', 1)
                ->where('id', '!=', $id)
                ->update(['is_petugas_barang' => 0]);
        }

        $oldNama   = $pejabat->nama;
        $oldAlamat = $pejabat->alamat;
        $pejabat->update($data);

        if (array_key_exists('nama', $data) && trim((string) $data['nama']) !== '' && $oldNama !== $pejabat->nama) {
            \App\Models\User::where('pejabat_id', $pejabat->id)->update(['nama' => $pejabat->nama]);
        }
        if (array_key_exists('alamat', $data) && $oldAlamat !== $pejabat->alamat) {
            \App\Models\User::where('pejabat_id', $pejabat->id)->update(['alamat' => $pejabat->alamat]);
        }

        return $pejabat;
    }

    public function delete($id)
    {
        $pejabat = Pejabat::find($id);
        if (!$pejabat) return false;

        // Cek relasi - Jenis Inventaris yang menggunakan pejabat ini
        $jenisCount = \App\Models\JenisInventarisPejabat::where('pejabat_id', $id)->count();
        if ($jenisCount > 0) {
            throw new \Exception("Tidak dapat menghapus pejabat '{$pejabat->nama}' karena masih digunakan sebagai pejabat penandatangan default di {$jenisCount} jenis inventaris. Hapus atau ganti pejabat tersebut terlebih dahulu.");
        }

        // Auto-null pejabat_id pada user yang terkait, lalu lanjut hapus
        $unlinkedUsers = \App\Models\User::where('pejabat_id', $id)->count();
        if ($unlinkedUsers > 0) {
            \App\Models\User::where('pejabat_id', $id)->update(['pejabat_id' => null]);
        }

        // Delete TTD file
        if ($pejabat->ttd_path && Storage::disk('public')->exists($pejabat->ttd_path)) {
            Storage::disk('public')->delete($pejabat->ttd_path);
        }
        
        $pejabat->delete();
        
        Log::info('Pejabat deleted successfully', [
            'pejabat_id' => $id,
            'nama' => $pejabat->nama,
            'unlinked_users' => $unlinkedUsers,
            'deleted_by' => auth()->id() ?? 'system',
        ]);

        return ['unlinked_users' => $unlinkedUsers];
    }

    protected function storeTtd($file)
    {
        try {
            $path = $file->store('ttd', 'public');
            return $path;
        } catch (\Exception $e) {
            Log::error('PejabatService::storeTtd error', ['error' => $e->getMessage()]);
            throw $e;
        }
    }


}
