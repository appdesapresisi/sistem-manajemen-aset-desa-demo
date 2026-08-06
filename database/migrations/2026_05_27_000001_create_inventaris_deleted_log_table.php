<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tabel inventaris_deleted_log — mencatat mutasi aset yang dihapus.
 * Digunakan untuk tracking "berkurang" di Laporan Aset.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('inventaris_deleted_log')) {
            return;
        }

        Schema::create('inventaris_deleted_log', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 100)->nullable();
            $table->string('nup', 100)->nullable();
            $table->string('nama_barang', 150)->nullable();
            $table->string('satuan', 50)->nullable();
            $table->decimal('nilai_perolehan', 15, 2)->nullable();
            $table->year('tahun_perolehan')->nullable();
            $table->timestamp('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventaris_deleted_log');
    }
};
