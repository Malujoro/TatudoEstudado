<?php

namespace App\Enums;

enum TipoSessao: string
{
    case Teoria = 'teoria';
    case Exercicios = 'exercicio';
    case Revisao = 'revisao';

    public function label(): string
    {
        return match ($this) {
            self::Teoria => 'Teoria',
            self::Exercicios => 'Exercícios',
            self::Revisao => 'Revisão',
        };
    }

    public function cor(): string
    {
        return match ($this) {
            self::Teoria => 'bg-blue-100 text-blue-700',
            self::Exercicios => 'bg-orange-100 text-orange-700',
            self::Revisao => 'bg-teal-100 text-teal-700',
        };
    }

    public function letra(): string
    {
        return match ($this) {
            self::Teoria => 'T',
            self::Exercicios => 'E',
            self::Revisao => 'R',
        };
    }
}
