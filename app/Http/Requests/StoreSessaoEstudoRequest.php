<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação de sessão de estudo.
 */
class StoreSessaoEstudoRequest extends FormRequest
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
            'data' => ['required', 'date'],
            'tipo' => ['required', 'string', 'max:255'],
            'horas' => ['required', 'numeric', 'min:0'],
            'finalizado' => ['sometimes', 'boolean'],
            'assunto_id' => ['required', 'uuid', 'exists:assuntos,id'],
        ];
    }
}
