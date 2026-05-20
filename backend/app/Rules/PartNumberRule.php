<?php

namespace App\Rules;

use App\Models\Part;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PartNumberRule implements ValidationRule
{
    /*
    protected Array $accepted_class;
    public function __construct(Array $accepted_class)
    {
        $this->accepted_class = $accepted_class;
    }
    */
    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $part = Part::where('part_number', $value)
        ->where('status', 'active')
        ->first();
        if (empty($part)) {
            $fail('Peça não encontrada,');
            return;
        } 
       /* elseif (!in_array($part->class_part, $this->accepted_class)) {
            $fail('A peça não pertence as classes. '.json_encode($this->accepted_class));
            return;
        }*/
    }
}
