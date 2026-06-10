<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Caderno;
use App\Models\Materia;
use App\Models\Metrica;
use App\Models\SessaoEstudo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para Assunto
 *
 * Cenários cobertos:
 * - cria assunto com UUID válido
 * - utiliza chave primária string
 * - não utiliza incremento automático
 * - fillable contém os campos esperados
 * - cast de teoria_finalizada para boolean
 * - cast de tipo para array
 * - materia(): pertence a uma matéria
 * - sessoesEstudo(): possui várias sessões
 * - caderno(): possui um caderno
 * - metrica(): possui uma métrica
 */
class AssuntoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usa_uuid_como_chave_primaria(): void
    {
        $assunto = Assunto::factory()->create();

        $this->assertIsString($assunto->id);
        $this->assertFalse($assunto->incrementing);
        $this->assertSame('string', $assunto->getKeyType());
    }

    public function test_fillable_esta_configurado_corretamente(): void
    {
        $assunto = new Assunto();

        $this->assertEquals([
            'nome',
            'materia_id',
            'teoria_finalizada',
            'tipo',
        ], $assunto->getFillable());
    }

    public function test_cast_teoria_finalizada_para_boolean(): void
    {
        $assunto = Assunto::factory()->create([
            'teoria_finalizada' => 1,
        ]);

        $this->assertIsBool($assunto->teoria_finalizada);
        $this->assertTrue($assunto->teoria_finalizada);
    }

    public function test_cast_tipo_para_array(): void
    {
        $assunto = Assunto::factory()->create([
            'tipo' => ['teoria', 'exercicio'],
        ]);

        $this->assertIsArray($assunto->tipo);
        $this->assertContains('teoria', $assunto->tipo);
        $this->assertContains('exercicio', $assunto->tipo);
    }

    public function test_pertence_a_uma_materia(): void
    {
        $materia = Materia::factory()->create();

        $assunto = Assunto::factory()->create([
            'materia_id' => $materia->id,
        ]);

        $this->assertTrue(
            $assunto->materia->is($materia)
        );
    }

    public function test_possui_varias_sessoes_de_estudo(): void
    {
        $assunto = Assunto::factory()->create();

        $sessao1 = SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
        ]);

        $sessao2 = SessaoEstudo::factory()->create([
            'assunto_id' => $assunto->id,
        ]);

        $this->assertCount(2, $assunto->sessoesEstudo);

        $this->assertTrue(
            $assunto->sessoesEstudo->contains($sessao1)
        );

        $this->assertTrue(
            $assunto->sessoesEstudo->contains($sessao2)
        );
    }

    public function test_possui_um_caderno(): void
    {
        $assunto = Assunto::factory()->create();

        $caderno = Caderno::factory()->create([
            'assunto_id' => $assunto->id,
        ]);

        $this->assertTrue(
            $assunto->caderno->is($caderno)
        );
    }

    public function test_possui_uma_metrica(): void
    {
        $assunto = Assunto::factory()->create();

        $metrica = Metrica::factory()->create([
            'assunto_id' => $assunto->id,
        ]);

        $this->assertTrue(
            $assunto->metrica->is($metrica)
        );
    }
}

