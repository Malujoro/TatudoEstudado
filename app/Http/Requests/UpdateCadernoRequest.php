<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation rules for updating a caderno.
 */
class UpdateCadernoRequest extends FormRequest
{
    /**
     * Authorize the request.
     *
     * Currently handled by route middleware (`auth`).
     *
     * @return bool
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
            'conteudo' => ['sometimes', 'string'],
        ];
    }
}
