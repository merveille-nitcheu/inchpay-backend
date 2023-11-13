<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SlugTrait;
use App\Models\User;

class Profil extends Model
{
    use HasFactory, SoftDeletes, SlugTrait;
    protected $fillable = ['slug', 'libelle'];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
