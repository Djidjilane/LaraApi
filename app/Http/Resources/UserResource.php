<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'lastname' => $this->lastname,
            'firstname' => $this->firstname,
            'telephone' => $this->telephone,
            'email' => $this->email,
            'photo' => $this->photo,
            'is_active' => $this->is_active,

            'roles' => $this->getRoleNames(),

            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
