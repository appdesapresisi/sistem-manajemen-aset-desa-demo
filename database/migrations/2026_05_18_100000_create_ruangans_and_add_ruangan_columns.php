<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Membuat tabel ruangans (lengkap dengan penanggung_jawab_id)
 * dan menambahkan kolom ruangan_id ke inventaris + requires_ruangan ke jenis_inventaris.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('ruangans')) {
            return;
        }

        Schema::create('ruangans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('kode', 50)->nullable()->unique();
            $table->text('deskripsi')->nullable();
            $table->unsignedBigInteger('penanggung_jawab_id')->nullable()->comment('FK ke users — penanggung jawab ruangan');
            $table->timestamps();

            $table->foreign('penanggung_jawab_id')->references('id')->on('users')->onDelete('set null')->onUpdate('cascade');
        });

        if (!Schema::hasColumn('inventaris', 'ruangan_id')) {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->foreignId('ruangan_id')->nullable()->constrained('ruangans')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('jenis_inventaris', 'requires_ruangan')) {
            Schema::table('jenis_inventaris', function (Blueprint $table) {
                $table->tinyInteger('requires_ruangan')->default(0)->after('custom_color');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('jenis_inventaris', 'requires_ruangan')) {
            Schema::table('jenis_inventaris', function (Blueprint $table) {
                $table->dropColumn('requires_ruangan');
            });
        }

        if (Schema::hasColumn('inventaris', 'ruangan_id')) {
            Schema::table('inventaris', function (Blueprint $table) {
                $table->dropForeign(['ruangan_id']);
                $table->dropColumn('ruangan_id');
            });
        }

        Schema::dropIfExists('ruangans');
    }
};
