<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bailes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 45);
            $table->enum('duracion', ['corta', 'media', 'larga'])->default('media');
            $table->foreignId('conjunto_id')->constrained('conjuntos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bailes');
    }
};