<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competicion_categoria', function (Blueprint $table) {
            $table->foreignId('competicion_id')->constrained('competicions')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->primary(['competicion_id', 'categoria_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competicion_categoria');
    }
};