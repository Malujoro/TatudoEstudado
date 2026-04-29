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
        Schema::create('sessoes_estudo', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->date('data');
            $table->string('tipo');
            $table->float('horas');
            $table->boolean('finalizado')->default(false);
            $table->foreignUuid('assunto_id')->constrained('assuntos')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sessoes_estudo');
    }
};
