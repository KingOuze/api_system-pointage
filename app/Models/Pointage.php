<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent; 
use Laravel\Sanctum\HasApiTokens;

class Pointage extends Eloquent
{
    use HasApiTokens;
    protected $collection = 'pointageusers'; // Nom de la collection MongoDB

    protected $fillable = [
        'user_id',
        'date',
        'checkin',
        'checkout',
        'etat',
        'TempsNormalDePointe'
    ];

     // Relation avec l'utilisateur
     public function user()
     {
         return $this->belongsTo(User::class, 'user_id', '_id');
     }
}
