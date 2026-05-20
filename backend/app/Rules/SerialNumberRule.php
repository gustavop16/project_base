<?php

namespace App\Rules;

use App\Models\Part;
use App\Models\PartReceiving;
use App\Models\PartReceivingItem;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class SerialNumberRule implements ValidationRule
{
    
    protected string $part_number;
    protected string $id_current;
    public function __construct(string $part_number, int $id_current)
    {
        $this->part_number = $part_number;
        $this->id_current  = $id_current;
    }
    
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
                
            $part = Part::where('part_number', $this->part_number)->first();
            
            if (in_array($part->class_part, ['Rotable', 'Tool', 'GSE'])) {

                //if($this->id_current){
                $exists = PartReceivingItem::where('serial_number', $value)
                    ->when($this->id_current, fn($q) => $q->where('id', '!=', $this->id_current))
                    ->whereHas('part', fn($q) => $q->where('class_part', $part->class_part))
                    ->exists();
                /*}
                else{
                    $exists = PartReceivingItem::where('serial_number', $value)
                    ->whereHas('part', function ($query) use ($part) {
                        $query->where('class_part', $part->class_part);
                    })->exists();
                }
                */
                if ($exists) {
                    $fail("O número de série já está em uso para peças da classe {$part->class_part}.");
                    return;
                }
            }
          
    }
}
