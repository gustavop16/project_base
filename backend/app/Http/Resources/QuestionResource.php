<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $options = collect($this->options ?? [])->values()->map(function ($opt, $index) {
            return [
                'id'             => $index + 1,
                'label'          => $opt['label'],
                'value'          => $opt['value'],
                'has_text_input' => (bool) ($opt['has_text_input'] ?? false),
            ];
        })->all();

        return [
            'id'          => $this->id,
            'question'    => $this->question,
            'description' => $this->description,
            'score'       => $this->score,
            'input_type'  => $this->input_type,
            'options'     => $options,
            'created_at'  => $this->created_at,
            'updated_at'  => $this->updated_at,
        ];
    }
}
