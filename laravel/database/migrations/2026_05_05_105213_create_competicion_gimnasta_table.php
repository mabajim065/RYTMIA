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
        Schema::create('competicion_gimnasta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competicion_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gimnasta_id')->constrained('gimnastas')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('competicion_gimnasta');
    }
};
