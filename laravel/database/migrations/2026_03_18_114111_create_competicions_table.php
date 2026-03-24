<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competicions', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 45);
            $table->date('fecha');
            $table->string('lugar', 45)->nullable();
            $table->enum('tipo', [
                'promesas', 'precopa', 'copa',
                'nacional_base', 'absoluto', 'exhibicion'
            ])->default('promesas');
            $table->enum('estado', [
                'pendiente', 'confirmada', 'inscrita', 'finalizada'
            ])->default('pendiente');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competicions');
    }
};