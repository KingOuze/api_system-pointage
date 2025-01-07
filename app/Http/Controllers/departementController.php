<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;

class departementController extends Controller
{
    //fonction pour créer un departement
    public function store(Request $request){
        // validation des champs
        $request->validate([
            'nom' =>'required|string|max:25',
            'description' =>'required|string|max:255',
        ]);

        // création du nouveau departement
        $departement = new Departement;
        $departement->nom = $request->nom;
        $departement->description = $request->description;
        $departement->save();

        // renvoi de la réponse
        return response()->json(['message'=>'Département créé avec succès','departement'=>$departement],201);
    }

    // fonction pour afficher tous les departements
    public function index(){
        // récupération des départements
        $departements = Departement::all();

        // renvoi de la réponse
        return response()->json(['departements'=>$departements],200);
    }

    // fonction pour afficher un département
    public function show($id){
        // récupération du département
        $departement = Departement::find($id);

        // vérification si le département existe
        if($departement){
            // renvoi de la réponse
            return response()->json(['departement'=>$departement],200);
        }else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Département introuvable'],404);
        }
    }

    // fonction pour modifier un département
    public function update(Request $request, $id){ 
        // récupération du département
        $departement = Departement::find($id);

        // vérification si le département existe
        if($departement){
            // validation des champs
            $request->validate([
                'nom' =>'required|string|max:25',
                'description' =>'required|string|max:255',
            ]);

            // mise à jour des données du département
            $departement->nom = $request->nom;
            $departement->description = $request->description;
            $departement->save();

            // renvoi de la réponse
            return response()->json(['message'=>'Département modifié avec succès','departement'=>$departement],200);
        } else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Département introuvable'],404);
        }
    }

    //fonction pour recuperer le nombre d'employés dans une departement
    public function getEmployeesCount($id){
        // récupération du département
        $departement = Departement::find($id);

        // vérification si le département existe
        if($departement){
            // récupération du nombre d'employés dans le département
            // Compter le nombre d'utilisateurs dans ce département
            $count = User::where('departement', $id)->count();

            // renvoi de la réponse
            return response()->json(['count'=>$count],200);
        } else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Département introuvable'],404);
        }
    }

    // fonction pour supprimer un département
    public function delete($id){
        // récupération du département
        $departement = Departement::find($id);

        // vérification si le département existe
        if($departement){
            // suppression du département
            $departement->delete();

            // renvoi de la réponse
            return response()->json(['message'=>'Département supprimé avec succès'],200);
        } else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Département introuvable'],404);
        }
    }

    //methode pour enrigistrer les données d'un fichier csv importé
    public function importDepartmentsFromCSV(Request $request){ 
         // Validation du fichier

        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        // Ouverture et lecture du fichier CSV
        $file = $request->file('file');
        $data = array_map('str_getcsv', file($file));

        // Suppression de l'en-tête
        array_shift($data);
        
        foreach ($data as $row) {
            // Assurez-vous que chaque ligne a le bon format
            if (count($row) == 2) {
                Departement::create([
                    'nom' => $row[0],
                    'description' => $row[1],
                ]);
            }
        }

        return response()->json(['message' => 'Données importées avec succès']);
    }
}