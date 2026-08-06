<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('kb_golongan')) {
            return;
        }

        // Level 1 — Golongan (1 digit)
        Schema::create('kb_golongan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('kode', 1)->unique();
            $table->string('uraian', 255);
        });

        // Level 2 — Bidang (2 digit)
        Schema::create('kb_bidang', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('golongan_id');
            $table->string('kode', 2);
            $table->string('uraian', 255);
            $table->unique(['golongan_id', 'kode']);
            $table->foreign('golongan_id')->references('id')->on('kb_golongan')->cascadeOnDelete();
        });

        // Level 3 — Kelompok (2 digit)
        Schema::create('kb_kelompok', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('bidang_id');
            $table->string('kode', 2);
            $table->string('uraian', 255);
            $table->unique(['bidang_id', 'kode']);
            $table->foreign('bidang_id')->references('id')->on('kb_bidang')->cascadeOnDelete();
        });

        // Level 4 — Sub Kelompok (2 digit)
        Schema::create('kb_sub_kelompok', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->unsignedSmallInteger('kelompok_id');
            $table->string('kode', 2);
            $table->string('uraian', 255);
            $table->unique(['kelompok_id', 'kode']);
            $table->foreign('kelompok_id')->references('id')->on('kb_kelompok')->cascadeOnDelete();
        });

        // Level 5 — Sub-Sub Kelompok (3 digit) — tabel utama untuk search
        Schema::create('kb_sub_sub_kelompok', function (Blueprint $table) {
            $table->mediumIncrements('id');
            $table->unsignedSmallInteger('sub_kelompok_id');
            $table->string('kode', 3);
            $table->string('uraian', 255);
            $table->string('kode_lengkap', 20); // e.g. "2.01.01.01.001"
            $table->unique(['sub_kelompok_id', 'kode']);
            $table->index('kode_lengkap');
            $table->index([DB::raw('uraian(100)')]);
            $table->foreign('sub_kelompok_id')->references('id')->on('kb_sub_kelompok')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kb_sub_sub_kelompok');
        Schema::dropIfExists('kb_sub_kelompok');
        Schema::dropIfExists('kb_kelompok');
        Schema::dropIfExists('kb_bidang');
        Schema::dropIfExists('kb_golongan');
    }
};
