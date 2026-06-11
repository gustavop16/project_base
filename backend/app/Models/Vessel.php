<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Vessel extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'call_sign',
        'port_of_registry',
        'gross_tonnage',
        'built_at',
        'imo_number',
        'active',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'built_at'      => 'date',
            'gross_tonnage' => 'float',
            'active'        => 'boolean',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
