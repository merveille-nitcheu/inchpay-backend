<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'slug' => $this->slug,
            'montant' => $this->montant,
            'trans_token' => $this->trans_token,
            'tel' => $this->tel,
            'status' => $this->status,
            'type_trans' => $this->type_trans,
            'created_at' => $this->created_at,
            'application' => [
                'nom' => $this->application->nom,
                'slug' => $this->application->slug,
                // Autres attributs de l'utilisateur que vous souhaitez inclure
            ],
            // 'application' => new ApplicationResource($this->application),

        ];
    }
}
