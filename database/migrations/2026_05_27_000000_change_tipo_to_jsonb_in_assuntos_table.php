<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE assuntos
            ALTER COLUMN tipo TYPE jsonb
            USING (
                CASE
                    WHEN tipo IS NULL OR tipo = '' THEN NULL
                    ELSE to_jsonb(ARRAY[tipo])
                END
            )
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement(<<<'SQL'
            ALTER TABLE assuntos
            ALTER COLUMN tipo TYPE varchar(255)
            USING (
                CASE
                    WHEN tipo IS NULL THEN NULL
                    ELSE (tipo->>0)
                END
            )
        SQL);
    }
};
