<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'token'            => $this->token,
            'status'           => $this->status,
            'monitoring_start' => $this->monitoring_start?->format('Y-m-d'),
            'monitoring_end'   => $this->monitoring_end?->format('Y-m-d'),
            'form'   => $this->when($this->relationLoaded('certificateForm'), fn() => [
                'id'   => $this->certificateForm->id,
                'name' => $this->certificateForm->name,
            ]),
            'vessel' => $this->when($this->relationLoaded('vessel'), fn() => [
                'id'   => $this->vessel->id,
                'name' => $this->vessel->name,
            ]),
            'created_by' => $this->when($this->relationLoaded('createdBy'), fn() => [
                'id'   => $this->createdBy->id,
                'name' => $this->createdBy->name,
            ]),
            'approved_by' => $this->when($this->relationLoaded('approvedBy'), fn() => $this->approvedBy
                ? ['id' => $this->approvedBy->id, 'name' => $this->approvedBy->name]
                : null),
            'approved_at'     => $this->approved_at,
            'rejected_reason' => $this->rejected_reason,
            'rejected_by'     => $this->when($this->relationLoaded('rejectedBy'), fn() => $this->rejectedBy
                ? ['id' => $this->rejectedBy->id, 'name' => $this->rejectedBy->name]
                : null),
            'rejected_at' => $this->rejected_at,
            'pdf_url'     => $this->pdf_path
                ? route('certificates.pdf', $this->id)
                : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
