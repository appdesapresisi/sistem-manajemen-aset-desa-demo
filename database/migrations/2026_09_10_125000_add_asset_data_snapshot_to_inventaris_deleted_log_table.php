<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('inventaris_deleted_log', function (Blueprint $table) {
            if (!Schema::hasColumn('inventaris_deleted_log', 'jenis_inventaris_nama')) {
                $table->string('jenis_inventaris_nama', 150)->nullable()->after('nama_barang');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'ruangan_nama')) {
                $table->string('ruangan_nama', 255)->nullable()->after('tahun_perolehan');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'nomor_sk')) {
                $table->string('nomor_sk', 150)->nullable()->after('ruangan_nama');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'alasan_penghapusan')) {
                $table->string('alasan_penghapusan', 100)->nullable()->after('nomor_sk');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('alasan_penghapusan');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'asset_data_snapshot')) {
                $table->json('asset_data_snapshot')->nullable()->after('keterangan');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'dokumen_pdf_path')) {
                $table->string('dokumen_pdf_path', 255)->nullable()->after('asset_data_snapshot');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'dokumen_pdf_name')) {
                $table->string('dokumen_pdf_name', 255)->nullable()->after('dokumen_pdf_path');
            }
            if (!Schema::hasColumn('inventaris_deleted_log', 'tanggal_hapus')) {
                $table->date('tanggal_hapus')->nullable()->after('dokumen_pdf_name');
            }
        });
    }

    public function down(): void
    {
        Schema::table('inventaris_deleted_log', function (Blueprint $table) {
            $columns = [
                'jenis_inventaris_nama', 'ruangan_nama', 'nomor_sk', 
                'alasan_penghapusan', 'keterangan', 'asset_data_snapshot', 
                'dokumen_pdf_path', 'dokumen_pdf_name', 'tanggal_hapus'
            ];
            foreach ($columns as $column) {
                if (Schema::hasColumn('inventaris_deleted_log', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
