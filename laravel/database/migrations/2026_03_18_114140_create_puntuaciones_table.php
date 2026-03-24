<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('puntuaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('competicion_id')->constrained('competicions')->onDelete('cascade');
            $table->foreignId('baile_id')->constrained('bailes')->onDelete('cascade');
            $table->foreignId('gimnasta_id')->nullable()->constrained('gimnastas')->onDelete('set null');
            $table->decimal('nota', 5, 3);
            $table->decimal('nota_artistica', 5, 3)->nullable();
            $table->decimal('nota_tecnica', 5, 3)->nullable();
            $table->text('comentario')->nullable();
            $table->date('fecha');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('puntuaciones');
    }
};