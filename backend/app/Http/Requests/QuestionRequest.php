<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'question'       => 'required|string|max:500',
            'description'    => 'nullable|string',
            'score'          => 'required|integer|min:0',
            'input_type'              => 'required|in:text,number,date,select_simple,select_multiple,radio,checkbox,upload',
            'options'                 => 'required_if:input_type,select_simple,select_multiple,radio,checkbox|nullable|array',
            'options.*.label'         => 'required|string|max:255',
            'options.*.value'         => 'required|string|max:255',
            'options.*.has_text_input'=> 'nullable|boolean',
        ];
    }
}
