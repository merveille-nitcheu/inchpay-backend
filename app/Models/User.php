<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Application;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Profil;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable,softDeletes,SlugTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['slug', 'nom', 'email', 'tel', 'username', 'password', 'status', 'photo', 'Isadmin','solde',"user_id"];
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function child()
    {
        return $this->hasMany(User::class);

    }
    public function parent()
    {
        return $this->BelongsTo(User::class);

    }

    public function applications()
    {
        return $this->hasMany(Application::class);

    }

    public function profils()
    {
        return $this->belongsToMany(Profil::class);

    }
}
