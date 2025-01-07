<?php

namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent; 
use Laravel\Sanctum\HasApiTokens;

class Departement extends Eloquent
{
    use HasApiTokens;
    protected $collection = 'departements'; // Nom de la collection MongoDB

    protected $fillable = [
        'nom',
        'descriptions'
    ];
}
