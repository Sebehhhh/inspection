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
        Schema::create('inspections', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to equipments table
            $table->unsignedBigInteger('equipment_id');
            $table->foreign('equipment_id')->references('id')->on('equipments')->onDelete('cascade');
            
            // Foreign key to indicators table
            $table->unsignedBigInteger('indicator_id');
            $table->foreign('indicator_id')->references('id')->on('indicators')->onDelete('cascade');
            
            // Tambahkan kolom problem_id sebagai foreign key ke tabel problems
            $table->unsignedBigInteger('problem_id')->nullable();
            $table->foreign('problem_id')->references('id')->on('problems')->onDelete('cascade');

            // Menyimpan nilai baseline dan real, gunakan tipe decimal sesuai kebutuhan presisi
            $table->decimal('baseline', 8, 2);
            $table->decimal('real', 8, 2);
            
            // Menggunakan boolean untuk status dengan default false
            $table->boolean('status')->default(false);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inspections');
    }
};
