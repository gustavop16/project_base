<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attachment extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'name',
        'file'
    ];

    /**
     * Relacionamento polimórfico reverso.
     */
    public function attachmentable(): MorphTo
    {
        return $this->morphTo();
    }
}
