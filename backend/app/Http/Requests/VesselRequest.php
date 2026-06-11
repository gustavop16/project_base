<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class VesselRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $vesselId = $this->route('vessel')?->id;

        return [
            'name'             => 'required|string|max:150',
            'call_sign'        => 'nullable|string|max:20',
            'port_of_registry' => 'nullable|string|max:100',
            'gross_tonnage'    => 'nullable|numeric|min:0',
            'built_at'         => 'nullable|date',
            'imo_number'       => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('vessels')->ignore($vesselId)->whereNull('deleted_at'),
            ],
            'active'           => 'boolean',
        ];
    }
}
