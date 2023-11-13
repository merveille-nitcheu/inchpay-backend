<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\WidgetResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\TransactionsResource;

class ApplicationResource extends JsonResource
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
            'produit' => $this->produit,
            'description' => $this->description,
            'url' => $this->url,
            'logo' => Storage::url($this->logo),
            'status' => $this->status,
            'widgets'=> WidgetResource::collection($this->widgets),
            'created_at' => $this->created_at,
            'transactions'=> TransactionsResource::collection($this->transactions),
            'user' => [
                'nom' => $this->user->nom,
                // Autres attributs de l'utilisateur que vous souhaitez inclure
            ],

        ];
    }
}
