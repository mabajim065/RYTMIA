<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('baile_aparato', function (Blueprint $table) {
            $table->foreignId('baile_id')->constrained('bailes')->onDelete('cascade');
            $table->foreignId('aparato_id')->constrained('aparatos')->onDelete('cascade');
            $table->primary(['baile_id', 'aparato_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('baile_aparato');
    }
};