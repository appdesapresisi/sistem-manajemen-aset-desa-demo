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
        if (!Schema::hasTable('inventaris_kir_values')) {
            Schema::create('inventaris_kir_values', function (Blueprint $table) {
                $table->id();
                $table->foreignId('inventaris_id')->constrained('inventaris')->onDelete('cascade')->onUpdate('cascade');
                $table->string('kir_field_key', 100);
                $table->text('value')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventaris_kir_values');
    }
};
