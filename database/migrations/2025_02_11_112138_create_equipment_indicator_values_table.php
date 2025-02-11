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
        Schema::create('equipment_indicator_values', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipment_id')->constrained('equipments')->onDelete('cascade');
            $table->unsignedBigInteger('indicator_id')->constrained('indicators')->onDelete('cascade');
            // Menyimpan nilai baseline dan real, gunakan tipe decimal sesuai kebutuhan presisi
            $table->decimal('baseline', 8, 2);
            $table->decimal('real', 8, 2);
            // Menggunakan enum untuk status dengan pilihan 'Yes' dan 'No'
            $table->boolean('status')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment_indicator_values');
    }
};
