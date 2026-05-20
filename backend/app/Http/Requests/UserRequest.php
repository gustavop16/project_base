<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        if ($this->has('photo')) {
            return [];
        }

        $userId = $this->route('user')?->id;

        return [
            'name'         => 'required|string|max:255',
            'email'        => [
                'required',
                'email',
                Rule::unique('users')->ignore($userId)->whereNull('deleted_at'),
            ],
            'phone'        => 'nullable|string|max:20',
            'type'         => ['required', Rule::in(array_keys(\App\Models\User::ARR_TYPE))],
            'observations' => 'nullable|string',
        ];
    }
}
