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
        Schema::create('tutores_legales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gimnasta_id')->constrained('gimnastas')->onDelete('cascade');
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('email');
            $table->string('relacion');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutores_legales');
    }
};
