<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateFormResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'active'      => $this->active,
            'questions_count' => $this->questions_count
                ?? ($this->relationLoaded('questions') ? $this->questions->count() : null),
            'questions'       => $this->when(
                $this->relationLoaded('questions'),
                fn() => $this->questions->map(fn($q) => [
                    'id'          => $q->id,
                    'order'       => $q->pivot->order,
                    'question'    => $q->question,
                    'description' => $q->description,
                    'score'       => $q->score,
                    'input_type'  => $q->input_type,
                    'options'     => $q->options ?? [],
                ])->values()
            ),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
