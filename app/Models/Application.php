<?php

namespace App\Models;

use App\Traits\SlugTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Widget;

class Application extends Model
{
    use HasFactory, SoftDeletes, SlugTrait;

    protected $fillable = ['slug', 'nom', 'categories', 'produit', 'description', 'status', 'url', 'logo', 'token','user_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function widgets()
    {
        return $this->hasMany(Widget::class);
    }

}
