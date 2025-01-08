<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\departementController;
use App\Http\Controllers\CohorteController; 


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


//------------- Route pour les Users --------------------------------

//Route pour créer un user
Route::post('/create-user', [UserController::class, 'store']);

//Route pour recupere tous les users
Route::get('/users', [UserController::class, 'index']);

//Route pour recupérer les users par leurs role
Route::get('/users/role/{role}', [UserController::class, 'getUsersByRole']);

//Route pour recupérer un user par son id
Route::get('/users/{id}', [UserController::class,'show']);

//Route pour asigner une carte RFID a un user
Route::post('/users/card/{id}', [UserController::class, 'assignRFIDCard']);

//Route pour supprimer une carte RFID a un user
Route::delete('/users/card/{id}', [UserController::class, 'unassignRFIDCard']);

//Route pour modifier un user
Route::put('/users/{id}', [UserController::class, 'update']);

// Route pour supprimer un utilisateur
Route::delete('/users/{id}', [UserController::class, 'deleteUser']);

// Route pour supprimer plusieurs utilisateurs
Route::delete('/users', [UserController::class, 'deleteMultipleUsers']);

//Route pour importer le fichier CSV pour l'enregistrement
Route::post('/users/import', [UserController::class, 'importUsersFromCSV']);



//------------ Routes pour le Département ------------------------------------


//recupere les users par department
Route::get('/users/departement/{departement}', [UserController::class, 'getUsersByDepartment']);

//route pour recupérer le nombre d'employés dans une department
Route::get('/departements/{id}/employees/count', [DepartementController::class, 'getEmployeesCount']);

//route pour recuperer les departments
Route::get('/departements', [DepartementController::class, 'index']);

//route pour recuperer un department
Route::get('/departements/{id}', [DepartementController::class, 'show']);

//route pour creer un departement
Route::post('/departements/create', [DepartementController::class, 'store']);

//route pour modifier un department
Route::put('/departements/update/{id}', [DepartementController::class, 'update']);

//route pour supprimer un department
Route::delete('/departements/delete/{id}', [DepartementController::class, 'delete']);

//route pour importer un fichier csv des department
Route::post('/departements/import', [DepartementController::class, 'importDepartmentsFromCSV']);


//----------------- Routes pour les Cohortes ------------------------

//recupere les users par Cohorte
Route::get('/users/cohorte/{cohorte}', [UserController::class, 'getUsersByCohorte']);

//route pour recupérer le nombre d'employés dans une Cohorte
Route::get('/cohortes/{id}/etudiant/count', [CohorteController::class, 'getEtudiantsCount']);

//route pour recuperer les Cohorte
Route::get('/cohortes', [CohorteController::class, 'index']);

//route pour recuperer un Cohorte
Route::get('/cohortes/{id}', [CohorteController::class, 'show']);

//route pour creer un Cohorte
Route::post('/cohortes/create', [CohorteController::class, 'store']);

//route pour modifier un Cohorte
Route::put('/cohortes/update/{id}', [CohorteController::class, 'update']);

//route pour supprimer un Cohorte
Route::delete('/cohortes/delete/{id}', [CohorteController::class, 'delete']);

//route pour importer un fichier csv des Cohorte
Route::post('/cohortes/import', [CohorteController::class, 'importCohortesFromCSV']);