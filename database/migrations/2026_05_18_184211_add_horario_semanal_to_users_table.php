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
        Schema::table('users', function (Blueprint $table) {
            $table->json('horario_semanal')->default(json_encode([
                'domingo' => 0,
                'segunda' => 0,
                'terca' => 0,
                'quarta' => 0,
                'quinta' => 0,
                'sexta' => 0,
                'sabado' => 0,
            ]))->after('role');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('horario_semanal');
        });
    }
};
