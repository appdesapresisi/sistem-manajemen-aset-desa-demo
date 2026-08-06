<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * DOKUMENTASI ONLY - SQL SUDAH DIJALANKAN LANGSUNG DI DATABASE
 * 
 * Migration ini dibuat untuk dokumentasi tabel `inventaris_laporan_aset_values`
 * yang menyimpan bulan perolehan untuk keperluan laporan aset per semester.
 * 
 * Tabel ini mengatasi masalah: ketika user input aset di tahun 2026 untuk
 * tahun perolehan 2024, sistem tidak tahu aset tersebut masuk Semester 1 atau 2.
 * 
 * Dengan bulan_perolehan (1-12):
 * - Bulan 1-6 = Semester 1
 * - Bulan 7-12 = Semester 2
 * - NULL = Default Semester 1
 */
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('inventaris_laporan_aset_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('inventaris_id');
            $table->tinyInteger('bulan_perolehan')->nullable()->comment('Bulan perolehan (1-12), NULL = default Semester 1');
            $table->timestamps();

            // Foreign key ke inventaris
            $table->foreign('inventaris_id')
                  ->references('id')
                  ->on('inventaris')
                  ->onDelete('cascade');

            // Index untuk performa query
            $table->index('bulan_perolehan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_laporan_aset_values');
    }
};
