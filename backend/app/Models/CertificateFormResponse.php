<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificateFormResponse extends Model
{
    use SoftDeletes;

    protected $fillable = ['certificate_form_id', 'certificate_id', 'user_id', 'status', 'observations'];

    public function certificateForm()
    {
        return $this->belongsTo(CertificateForm::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function answers()
    {
        return $this->hasMany(CertificateFormResponseAnswer::class);
    }
}
