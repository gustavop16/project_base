<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VesselResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'name'             => $this->name,
            'call_sign'        => $this->call_sign,
            'port_of_registry' => $this->port_of_registry,
            'gross_tonnage'    => $this->gross_tonnage,
            'built_at'         => $this->built_at?->format('d/m/Y'),
            'imo_number'       => $this->imo_number,
            'active'           => $this->active,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
        ];
    }
}
