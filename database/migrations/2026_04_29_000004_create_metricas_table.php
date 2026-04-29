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
        Schema::create('metricas', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->unsignedInteger('acertos')->default(0);
            $table->unsignedInteger('erros')->default(0);
            $table->foreignUuid('assunto_id')->constrained('assuntos')->cascadeOnDelete()->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metricas');
    }
};
