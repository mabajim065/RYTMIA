<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('competicion_conjunto', function (Blueprint $table) {
            $table->foreignId('competicion_id')->constrained('competicions')->cascadeOnDelete();
            $table->foreignId('conjunto_id')->constrained('conjuntos')->cascadeOnDelete();
            $table->primary(['competicion_id', 'conjunto_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('competicion_conjunto');
    }
};
