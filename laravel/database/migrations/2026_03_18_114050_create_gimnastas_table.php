<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gimnastas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('club_id')->constrained('clubs')->onDelete('cascade');
            $table->foreignId('conjunto_id')->nullable()->constrained('conjuntos')->onDelete('set null');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('numero_licencia')->unique()->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('anios_en_club')->default(0);
            $table->enum('estado', ['activa', 'inactiva', 'baja'])->default('activa');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gimnastas');
    }
};