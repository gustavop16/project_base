<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Certificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'certificate_form_id', 'vessel_id', 'token', 'status', 'pdf_path',
        'monitoring_start', 'monitoring_end',
        'created_by', 'approved_by', 'approved_at',
        'rejected_reason', 'rejected_by', 'rejected_at',
    ];

    protected function casts(): array
    {
        return [
            'monitoring_start' => 'date',
            'monitoring_end'   => 'date',
            'approved_at'      => 'datetime',
            'rejected_at'      => 'datetime',
        ];
    }

    public function certificateForm()
    {
        return $this->belongsTo(CertificateForm::class);
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function rejectedBy()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function responses()
    {
        return $this->hasMany(CertificateFormResponse::class);
    }
}
