<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('inventaris_deleted_log', function (Blueprint $table) {
            if (!Schema::hasColumn('inventaris_deleted_log', 'merk_type')) {
                $table->string('merk_type', 150)->nullable()->after('satuan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventaris_deleted_log', function (Blueprint $table) {
            if (Schema::hasColumn('inventaris_deleted_log', 'merk_type')) {
                $table->dropColumn('merk_type');
            }
        });
    }
};
