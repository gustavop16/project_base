<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificateFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'description'    => 'nullable|string',
            'active'         => 'boolean',
            'questions'      => 'nullable|array',
            'questions.*.id' => 'required|integer|exists:questions,id',
        ];
    }
}
