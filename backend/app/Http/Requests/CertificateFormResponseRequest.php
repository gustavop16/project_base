<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificateFormResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'certificate_form_id'       => 'required|integer|exists:certificate_forms,id',
            'observations'              => 'nullable|string',
            'answers'                   => 'required|array',
            'answers.*.question_id'     => 'required|integer|exists:questions,id',
            'answers.*.answer'          => 'nullable',
            'answers.*.text_complement' => 'nullable|string',
        ];
    }
}
