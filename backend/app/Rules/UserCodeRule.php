<?php

namespace App\Rules;

use App\Models\Part;
use App\Models\User;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class UserCodeRule implements ValidationRule
{    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $user = User::where('user_code', $value)->first();
        if (empty($user)) {
            $fail('Usuário não encontrado');
            return;
        } 
    }
}
