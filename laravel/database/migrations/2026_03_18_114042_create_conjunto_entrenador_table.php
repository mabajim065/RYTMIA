<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conjunto_entrenador', function (Blueprint $table) {
            $table->foreignId('conjunto_id')->constrained('conjuntos')->onDelete('cascade');
            $table->foreignId('entrenador_id')->constrained('entrenadores')->onDelete('cascade');
            $table->primary(['conjunto_id', 'entrenador_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('conjunto_entrenador');
    }
};