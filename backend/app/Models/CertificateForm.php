<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateForm extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['name', 'description', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function questions()
    {
        return $this->belongsToMany(Question::class, 'certificate_form_questions')
            ->withPivot('order')
            ->orderByPivot('order');
    }
}
