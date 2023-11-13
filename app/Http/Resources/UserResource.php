<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\ApplicationResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [

            'nom' => ucfirst($this->nom),
            'slug' => $this->slug,
            'username' => $this->username,
            'email' => $this->email,
            'photo' => Storage::url($this->photo),
            'status' => $this->status,
            'Isadmin' => $this->Isadmin,
            'solde' => $this->solde,
            'applications'=> ApplicationResource::collection($this->applications),
            'created_at' => $this->created_at,
            

        ];
    }
}
