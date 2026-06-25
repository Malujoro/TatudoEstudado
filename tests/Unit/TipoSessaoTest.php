<?php

namespace Tests\Unit;

use App\Enums\TipoSessao;
use PHPUnit\Framework\TestCase;

class TipoSessaoTest extends TestCase
{
    // Backing values
    public function test_teoria_tem_value_correto(): void
    {
        $this->assertSame('teoria', TipoSessao::Teoria->value);
    }

    public function test_exercicios_tem_value_correto(): void
    {
        $this->assertSame('exercicio', TipoSessao::Exercicios->value);
    }

    public function test_revisao_tem_value_correto(): void
    {
        $this->assertSame('revisao', TipoSessao::Revisao->value);
    }

    // label()
    public function test_label_teoria(): void
    {
        $this->assertSame('Teoria', TipoSessao::Teoria->label());
    }

    public function test_label_exercicios(): void
    {
        $this->assertSame('Exercícios', TipoSessao::Exercicios->label());
    }

    public function test_label_revisao(): void
    {
        $this->assertSame('Revisão', TipoSessao::Revisao->label());
    }

    // cor()
    public function test_cor_teoria(): void
    {
        $this->assertSame('bg-blue-100 text-blue-700', TipoSessao::Teoria->cor());
    }

    public function test_cor_exercicios(): void
    {
        $this->assertSame('bg-orange-100 text-orange-700', TipoSessao::Exercicios->cor());
    }

    public function test_cor_revisao(): void
    {
        $this->assertSame('bg-teal-100 text-teal-700', TipoSessao::Revisao->cor());
    }

    // letra()
    public function test_letra_teoria(): void
    {
        $this->assertSame('T', TipoSessao::Teoria->letra());
    }

    public function test_letra_exercicios(): void
    {
        $this->assertSame('E', TipoSessao::Exercicios->letra());
    }

    public function test_letra_revisao(): void
    {
        $this->assertSame('R', TipoSessao::Revisao->letra());
    }

    // from()
    public function test_from_instancia_teoria(): void
    {
        $this->assertSame(TipoSessao::Teoria, TipoSessao::from('teoria'));
    }

    public function test_from_instancia_exercicios(): void
    {
        $this->assertSame(TipoSessao::Exercicios, TipoSessao::from('exercicio'));
    }

    public function test_from_instancia_revisao(): void
    {
        $this->assertSame(TipoSessao::Revisao, TipoSessao::from('revisao'));
    }
}
