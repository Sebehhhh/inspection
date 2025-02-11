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
        Schema::create('further_testing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('problem_id')->constrained('problems')->onDelete('cascade');
            // Menyimpan metode pengujian lanjutan
            $table->string('further_testing_method');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('further_testing');
    }
};
