<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateFormResponseAnswer extends Model
{
    use SoftDeletes;
    protected $fillable = ['certificate_form_response_id', 'question_id', 'answer', 'text_complement'];

    protected function casts(): array
    {
        return ['answer' => 'json'];
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
