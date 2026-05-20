<?php

namespace App\Http\Requests;

use Illuminate\Container\Attributes\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {        
        if ($this->has('status')) {
            return [
                'status' => 'required|in:active,inactive',
            ];
        }
        elseif ($this->has('photo')) {
            return [];
        }

        return [
            'name' => 'required|string|max:255',
            'bay_id' => 'required',
            'email' => [
                    'required',
                    'email',
                    Rule::unique('users')->ignore($this->user?->id)->whereNull('deleted_at')
                ],
        ];
    }
}
