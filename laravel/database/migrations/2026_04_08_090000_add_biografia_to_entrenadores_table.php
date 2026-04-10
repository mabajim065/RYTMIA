<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('entrenadores', function (Blueprint $table) {
            $table->text('biografia')->nullable()->after('titulacion');
            $table->string('foto_url')->nullable()->after('biografia');
        });
    }

    public function down(): void
    {
        Schema::table('entrenadores', function (Blueprint $table) {
            $table->dropColumn(['biografia', 'foto_url']);
        });
    }
};
