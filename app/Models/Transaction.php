<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\SlugTrait;

class Transaction extends Model
{
    use HasFactory, SoftDeletes, SlugTrait;

    protected $fillable = ['slug', 'montant','trans_token','tel','type_trans','application_id','status'];


    public function application()
    {
        return $this->belongsTo(Application::class);
    }

}
