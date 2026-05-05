<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para criação de caderno.
 */
class StoreCadernoRequest extends FormRequest
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
            'conteudo' => ['required', 'string'],
            'assunto_id' => ['required', 'uuid', 'exists:assuntos,id', 'unique:cadernos,assunto_id'],
        ];
    }
}
