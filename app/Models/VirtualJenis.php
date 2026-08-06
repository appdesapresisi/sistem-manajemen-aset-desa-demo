<?php

namespace App\Models;

/**
 * Virtual Jenis Model
 * 
 * Merepresentasikan "jenis inventaris" yang tidak tersimpan di database,
 * melainkan dicompute/aggregate dari data sistem.
 * 
 * NOTED (Apr 2026): Buku Inventaris Aset Desa sekarang disimpan di database (jenis_inventaris table)
 * sebagai virtual jenis dengan is_pejabat relationship ke jenis_inventaris_pejabats.
 * Model ini hanya untuk future virtual jenis yang tidak perlu database backend.
 * 
 * Virtual jenis ini digunakan untuk:
 * 1. Export dropdown UI di export-data/index.blade.php (untuk future virtual categories)
 * 2. Export Log display di dashboard (jika jenis_inventaris_id = null)
 * 3. Legacy support untuk custom virtual aggregations
 * 
 * @author Implemented Q1 2026
 */
class VirtualJenis
{
    /**
     * Semua virtual jenis yang tersedia (sekarang kosong)
     * 
     * NOTE: Buku Inventaris Aset Desa dipindah ke jenis_inventaris table (DB-backed)
     * untuk support pejabat customization dan scalability.
     * 
     * Jika ada virtual jenis baru di masa depan, tambahkan di sini.
     */
    public static function all(): array
    {
        return [
            // VirtualJenis sekarang kosong - semua dihandle via jenis_inventaris table
            // Untuk future virtual categories, tambahkan const dan return di sini
        ];
    }

    /**
     * Get virtual jenis by nama
     * 
     * @param string $nama
     * @return array|null
     */
    public static function getByNama(string $nama): ?array
    {
        foreach (self::all() as $jenis) {
            if ($jenis['nama'] === $nama) {
                return $jenis;
            }
        }
        return null;
    }

    /**
     * Get virtual jenis by ID
     * 
     * @param string $id
     * @return array|null
     */
    public static function getById(string $id): ?array
    {
        foreach (self::all() as $jenis) {
            if ($jenis['id'] === $id) {
                return $jenis;
            }
        }
        return null;
    }

    /**
     * Check if given jenis ID is virtual
     * 
     * @param string $id
     * @return bool
     */
    public static function isVirtual(string $id): bool
    {
        return self::getById($id) !== null;
    }

    /**
     * Get virtual jenis for export dropdown UI
     * Format: [ ['id' => ..., 'nama' => ..., 'type' => 'virtual'], ... ]
     */
    public static function forDropdown(): array
    {
        return array_map(function ($jenis) {
            return [
                'id' => $jenis['id'],
                'nama' => $jenis['nama'],
                'type' => $jenis['type'],
                'color' => $jenis['color'] ?? null,
            ];
        }, self::all());
    }
}
