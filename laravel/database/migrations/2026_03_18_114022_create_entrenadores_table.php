<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrenadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->string('titulacion')->nullable();
            $table->integer('anios_experiencia')->default(0);
            $table->integer('horas_semanales')->default(0);
            // Añadido 'baja' para que coincida con gimnastas y con el resto del código
            $table->enum('estado', ['activa', 'inactiva', 'baja'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('entrenadores');
    }
};