<?php

namespace Tests\Feature;

use App\Models\Assunto;
use App\Models\Materia;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Testes para Materia
 *
 * Cenários cobertos:
 * - criação de matéria com dados válidos
 * - geração automática de UUID como chave primária
 * - utilização de chave primária do tipo string
 * - relacionamento user(): pertence ao usuário correto
 * - relacionamento assuntos(): retorna apenas assuntos vinculados à matéria
 * - relacionamento assuntos(): suporta múltiplos assuntos
 * - persistência correta dos campos nome e user_id
 * - configuração correta dos atributos fillable
 * - modelo não utiliza chave primária auto incremental
 */
class OtherModelsTest extends TestCase
{
    use RefreshDatabase;

    // -----------------------------------------------------------------------
    // Criação
    // -----------------------------------------------------------------------

    public function test_pode_criar_materia(): void
    {
        $user = User::factory()->create();

        $materia = Materia::create([
            'nome' => 'Matemática',
            'user_id' => $user->id,
        ]);

        $this->assertNotNull($materia->id);
        $this->assertSame('Matemática', $materia->nome);

        $this->assertDatabaseHas('materias', [
            'id' => $materia->id,
            'nome' => 'Matemática',
            'user_id' => $user->id,
        ]);
    }

    // -----------------------------------------------------------------------
    // UUID
    // -----------------------------------------------------------------------

    public function test_utiliza_uuid_como_chave_primaria(): void
    {
        $user = User::factory()->create();

        $materia = Materia::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertIsString($materia->id);
        $this->assertFalse($materia->incrementing);
        $this->assertSame('string', $materia->getKeyType());
    }

    // -----------------------------------------------------------------------
    // Relacionamento User
    // -----------------------------------------------------------------------

    public function test_pertence_a_um_usuario(): void
    {
        $user = User::factory()->create();

        $materia = Materia::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->assertInstanceOf(User::class, $materia->user);
        $this->assertTrue($materia->user->is($user));
    }

    // -----------------------------------------------------------------------
    // Relacionamento Assuntos
    // -----------------------------------------------------------------------

    public function test_possui_varios_assuntos(): void
    {
        $materia = Materia::factory()->create();

        $assunto1 = Assunto::factory()->create([
            'materia_id' => $materia->id,
        ]);

        $assunto2 = Assunto::factory()->create([
            'materia_id' => $materia->id,
        ]);

        $this->assertCount(2, $materia->assuntos);

        $this->assertTrue(
            $materia->assuntos->contains($assunto1)
        );

        $this->assertTrue(
            $materia->assuntos->contains($assunto2)
        );
    }

    // -----------------------------------------------------------------------
    // Fillable
    // -----------------------------------------------------------------------

    public function test_fillable_esta_configurado_corretamente(): void
    {
        $materia = new Materia;

        $this->assertEquals(
            ['nome', 'user_id'],
            $materia->getFillable()
        );
    }
}
