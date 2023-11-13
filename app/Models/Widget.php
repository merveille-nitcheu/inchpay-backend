<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SlugTrait;
use App\Models\Application;

class Widget extends Model
{
    use HasFactory, SoftDeletes, SlugTrait;

    protected $fillable = ['slug','nom','url_redirection','status','lien_payement','application_id'];


    public function application()
    {
        return $this->belongsTo(Application::class);
    }


}
