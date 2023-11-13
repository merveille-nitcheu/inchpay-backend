<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ApplicationResource;

class WidgetResource extends JsonResource
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
            'url_redirection'=>$this->url_redirection,
            'lien_payement'=>$this->lien_payement,
            'status'=>$this->status,
            'created_at' => $this->created_at,
            'application' => [
                'nom' => $this->application->nom,
                // Autres attributs de l'utilisateur que vous souhaitez inclure
            ],

        ];
    }
}
