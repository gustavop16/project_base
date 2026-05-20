<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'email'        => $this->email,
            'phone'        => $this->phone,
            'type'         => $this->type,
            'type_br'      => User::ARR_TYPE[$this->type] ?? $this->type,
            'observations' => $this->observations,
            'photo'        => $this->photo,
            'active'       => $this->active,
        ];
    }
}
