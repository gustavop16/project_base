<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Question extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'description',
        'score',
        'input_type',
        'options',
    ];

    protected function casts(): array
    {
        return [
            'score'   => 'integer',
            'options' => 'array',
        ];
    }
}
