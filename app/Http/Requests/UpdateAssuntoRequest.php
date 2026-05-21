<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for updating an assunto.
 */
class UpdateAssuntoRequest extends FormRequest
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
            'nome' => ['sometimes', 'string', 'max:255'],
            'teoria_finalizada' => ['sometimes', 'boolean'],
        ];
    }
}
