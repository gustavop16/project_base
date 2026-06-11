<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CertificateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'certificate_form_id' => 'required|integer|exists:certificate_forms,id',
            'vessel_id'           => 'required|integer|exists:vessels,id',
            'monitoring_start'    => 'required|date',
            'monitoring_end'      => 'required|date|after:monitoring_start',
        ];
    }
}
