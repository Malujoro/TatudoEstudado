<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for creating a caderno.
 */
class StoreCadernoRequest extends FormRequest
{
    /**
     * Authorize the request.
     *
     * Currently handled by route middleware (`auth`).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules.
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
