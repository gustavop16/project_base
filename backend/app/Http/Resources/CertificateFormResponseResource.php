<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateFormResponseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'status'           => $this->status,
            'observations'     => $this->observations,
            'certificate_form' => [
                'id'   => $this->certificateForm->id,
                'name' => $this->certificateForm->name,
            ],
            'user' => $this->user
                ? ['id' => $this->user->id, 'name' => $this->user->name]
                : null,
            'answers_count' => $this->answers_count
                ?? ($this->relationLoaded('answers') ? $this->answers->count() : null),
            'answers' => $this->when(
                $this->relationLoaded('answers'),
                fn() => $this->answers->map(fn($a) => [
                    'id'             => $a->id,
                    'question_id'    => $a->question_id,
                    'answer'         => $a->answer,
                    'text_complement'=> $a->text_complement,
                ])
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
