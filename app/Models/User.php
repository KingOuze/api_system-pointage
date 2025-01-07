<?php
namespace App\Models;

use MongoDB\Laravel\Eloquent\Model as Eloquent; 
use Laravel\Sanctum\HasApiTokens;

class User extends Eloquent
{

    use HasApiTokens;
    protected $collection = 'users'; // Nom de la collection MongoDB

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'photo',
        'email',
        'addresse',
        'role',
        'matricule',
        'cardId',
        'password',
        'departement',
        'fonction',
        'cohorte',
    ];

    protected $hidden = ['password'];
}