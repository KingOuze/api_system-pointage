<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pointage;
use App\Models\User;

class PointageController extends Controller
{
    //Recuperer toutes les pointages
    public function index()
    {
        $users = User::all();
        $pointages = Pointage::all();
    
        // Associer les pointages aux utilisateurs et filtrer ceux qui n'ont pas de pointages
        $result = $users->map(function ($user) use ($pointages) {
            $userPointages = $pointages->filter(function ($p) use ($user) {
                return (string) $p->user_id === (string) $user->_id;
            })->values(); // Réinitialise les clés
    
            // Ne retourner que les utilisateurs ayant au moins un pointage
            if ($userPointages->isNotEmpty()) {
                return [
                    'user' => [
                        'matricule' => $user->matricule,
                        'nom' => $user->nom,
                        'prenom' => $user->prenom,
                        'role' => $user->role
                    ],
                    'pointages' => $userPointages,
                ];
            }
    
            return null; // Retourne null pour les utilisateurs sans pointages
        })->filter(); // Filtrer les résultats pour enlever les utilisateurs sans pointages
    
        if ($result->isEmpty()) {
            return response()->json(['error' => 'No users with pointages found'], 404);
        }
    
        return response()->json(['success' => true, 'data' => $result], 200);
    }

    //Recuperer un pointage par id
    public function show($id){  
        $pointage = Pointage::find($id);
        if(!$pointage){
            return response()->json(['error' => 'Pointage not found'], 404);
        }
        return response()->json($pointage, 200);
    }
}
