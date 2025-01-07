<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent; 
use Laravel\Sanctum\HasApiTokens;

class Cohorte extends Eloquent
{
    use HasApiTokens;
    protected $collection = 'cohortes'; // Nom de la collection MongoDB

    protected $fillable = [
        'nom',
        'descriptions'
    ];
}
