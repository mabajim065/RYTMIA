<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('gimnasta_id')->constrained('gimnastas')->onDelete('cascade');
            $table->decimal('importe', 8, 2);
            $table->date('fecha_pago');
            $table->date('fecha_vencimiento')->nullable();
            $table->enum('estado', ['pagado', 'pendiente', 'vencido'])->default('pendiente');
            $table->enum('concepto', ['cuota_mensual', 'competicion', 'equipacion', 'otro'])->default('cuota_mensual');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};