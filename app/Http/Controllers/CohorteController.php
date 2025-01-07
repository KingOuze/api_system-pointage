<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cohorte;
use App\Models\User;

class CohorteController extends Controller
{
     //fonction pour créer un cohorte
     public function store(Request $request){
        // validation des champs
        $request->validate([
            'nom' =>'required|string|max:25',
            'description' =>'required|string|max:255',
        ]);

        // création du nouveau cohorte
        $cohorte = new Cohorte;
        $cohorte->nom = $request->nom;
        $cohorte->description = $request->description;
        $cohorte->save();

        // renvoi de la réponse
        return response()->json(['message'=>'Cohorte créé avec succès','cohorte'=>$cohorte],200);
    }

    // fonction pour afficher tous les cohortes
    public function index(){
        // récupération des départements
        $cohortes = Cohorte::all();

        // renvoi de la réponse
        return response()->json(['cohortes'=>$cohortes],200);
    }

    // fonction pour afficher un cohorte
    public function show($id){
        // récupération du cohorte
        $cohorte = Cohorte::find($id);

        // vérification si le cohorte existe
        if($cohorte){
            // renvoi de la réponse
            return response()->json(['cohorte'=>$cohorte],200);
        }else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Cohorte introuvable'],404);
        }
    }

    // fonction pour modifier un cohorte
    public function update(Request $request, $id){ 
        // récupération du cohorte
        $cohorte = Cohorte::find($id);

        // vérification si le cohorte existe
        if($cohorte){
            // validation des champs
            $request->validate([
                'nom' =>'required|string|max:25',
                'description' =>'required|string|max:255',
            ]);

            // mise à jour des données du cohorte
            $cohorte->nom = $request->nom;
            $cohorte->description = $request->description;
            $cohorte->save();

            // renvoi de la réponse
            return response()->json(['message'=>'Cohorte modifié avec succès','cohorte'=>$cohorte],200);
        } else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Cohorte introuvable'],404);
        }
    }

    //fonction pour recuperer le nombre d'employés dans une cohorte
    public function getEtudiantsCount($id){
        // récupération du cohorte
        $cohorte = Cohorte::find($id);

        // vérification si le cohorte existe
        if($cohorte){
            // récupération du nombre d'employés dans le cohorte
            // Compter le nombre d'utilisateurs dans ce cohorte
            $count = User::where('cohorte', $id)->count();

            // renvoi de la réponse
            return response()->json(['count'=>$count],200);
        } else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Cohorte introuvable'],404);
        }
    }

    // fonction pour supprimer un Cohorte
    public function delete($id){
        // récupération du Cohorte
        $cohorte = Cohorte::find($id);

        // vérification si le Cohorte existe
        if($cohorte){
            // suppression du Cohorte
            $cohorte->delete();

            // renvoi de la réponse
            return response()->json(['message'=>'Cohorte supprimé avec succès'],200);
        } else{
            // renvoi d'une erreur 404
            return response()->json(['message'=>'Cohorte introuvable'],404);
        }
    }

    //methode pour enrigistrer les données d'un fichier csv importé
    public function importCohortesFromCSV(Request $request){ 
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
                Cohorte::create([
                    'nom' => $row[0],
                    'description' => $row[1],
                ]);
            }
        }

        return response()->json(['message' => 'Données importées avec succès']);
    }
}
