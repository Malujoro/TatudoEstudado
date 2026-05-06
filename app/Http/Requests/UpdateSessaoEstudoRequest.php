<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para atualização de sessão de estudo.
 */
class UpdateSessaoEstudoRequest extends FormRequest
{
    /**
     * No momento, a autorização é controlada por middleware de rota (`auth`).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regras de validação.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'data' => ['sometimes', 'date'],
            'tipo' => ['sometimes', 'string', 'max:255'],
            'horas' => ['sometimes', 'numeric', 'min:0'],
            'finalizado' => ['sometimes', 'boolean'],
        ];
    }
}
