<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competicion_baile', function (Blueprint $table) {
            $table->foreignId('competicion_id')->constrained('competicions')->onDelete('cascade');
            $table->foreignId('baile_id')->constrained('bailes')->onDelete('cascade');
            $table->primary(['competicion_id', 'baile_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competicion_baile');
    }
};