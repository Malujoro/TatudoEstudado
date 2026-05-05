<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validação para atualização de assunto.
 */
class UpdateAssuntoRequest extends FormRequest
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
            'nome' => ['sometimes', 'string', 'max:255'],
        ];
    }
}
