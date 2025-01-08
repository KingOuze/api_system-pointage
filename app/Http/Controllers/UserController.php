<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Fonction pour créer un utilisateur
    public function store(Request $request)
    {
        // Valider les champs communs
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'photo' => 'nullable|String',
            'email' => 'required|string|email|max:255|unique:users',
            'addresse' => 'required|string|max:255',
            'role' => 'required|string|in:admin,vigile,employe,etudiant',
        ]);

        

        // Gérer l'upload de la photo
        //$photoPath = $request->hasFile('photo') ? $request->file('photo')->store('photos', 'public') : null;

        // Préparer les données communes
        $userData = [
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'photo' => $request->photo,
            'email' => $request->email,
            'addresse' => $request->addresse,
            'role' => $request->role,
        ];

        // Ajouter les champs spécifiques en fonction du rôle
        switch ($request->role) {
            case 'admin':
                $request->validate([
                    'cardId' => 'nullable|string|max:255|unique:users',
                    'password' => 'required|string|min:8',
                ]);
                $userData['cardId'] = $request->cardId;
                $userData['password'] = bcrypt($request->password);
                // Autogénérer un matricule
                $matricule = 'MAT_ADM-' . strtoupper(uniqid());
                $userData['matricule'] = $matricule;
                break;

            case 'vigile':
                $request->validate([
                    'password' => 'required|string|min:8',
                ]);
                $userData['password'] = bcrypt($request->password)::make($request->password);
                // Autogénérer un matricule
                $matricule = 'MAT_VIG-' . strtoupper(uniqid());
                $userData['matricule'] = $matricule;
                break;

            case 'employe':
                $request->validate([
                    'departement' => 'required|string|max:255',
                    'fonction' => 'required|string|max:255',
                    'cardId' => 'nullable|string|max:255|unique:users',
                ]);
                $userData['departement'] = $request->departement;
                $userData['fonction'] = $request->fonction;
                $userData['cardId'] = $request->cardId;
                // Autogénérer un matricule
                $matricule = 'MAT_EMP-' . strtoupper(uniqid());
                $userData['matricule'] = $matricule;
                break;

            case 'etudiant':
                $request->validate([
                    'cohorte' => 'required|string|max:255',
                    'cardId' => 'nullable|string|max:255|unique:users',
                ]);
                $userData['cohorte'] = $request->cohorte;
                $userData['cardId'] = $request->cardId;
                // Autogénérer un matricule
                $matricule = 'MAT_ETU-' . strtoupper(uniqid());
                $userData['matricule'] = $matricule;
                break;

            default:
                return response()->json(['error' => 'Rôle invalide'], 400);
        }


        // Enregistrer l'utilisateur dans MongoDB
        $user = User::create($userData);

        return response()->json(['message' => 'Utilisateur créé avec succès', 'user' => $user], 201);
    }

    //Recuperation de tous les users
    public function index(){
        // Fetch all users
        $users = User::all();

        // Return the users
        return response()->json(['users' => $users], 200);
    }

    //Récupération des utilisateurs par leurs role
    public function getUsersByRole(string $role)
    {
        // Validate the role
        if (!in_array($role, ['admin', 'vigile', 'employe', 'etudiant'])) {
            return response()->json(['error' => 'Invalid role'], 400);
        }

        // Fetch users by role
        $users = User::where('role', $role)->get();

        // Return the users
        return response()->json(['users' => $users], 200);
    }

    //Récupération d'un user par son id
    public function show(string $id){
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Return the user
        return response()->json(['user' => $user], 200);
    }

    //Affectation d'une carte RFID a un user
    public function assignRFIDCard(string $id, Request $request){
        
        $userExist = User::where('cardId', $request->cardId)->get();

        if ($userExist->count() > 0) {
            return response()->json(['error' => 'Carte RFID déjà utilisée', 'user' => $request], 400);
        }
        // Find the user by ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validate the cardId
        $request->validate([
            'cardId' => 'nullable|string|max:255|unique:users,cardId,'. $user->id,
        ]);
        $user->cardId = $request->cardId;
        $user->save();
        return response()->json(['message' => 'Carte RFID affectée avec succès', 'user' => $user], 200);
    }

    //Supprimer l'assignation d'un user
    public function unassignRFIDCard(string $id){
        
        // Find the user by ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->cardId = null;
        $user->save();
        return response()->json(['message' => 'Carte RFID désinscrite avec succès', 'user' => $user], 200);
    }

    //Mise a jour des informations d'un user
    public function update(Request $request, string $id)
    {
        // Find the user by ID
        $user = User::find($id);
        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }
    
        // Validate common fields
        $request->validate([
            'nom' => 'nullable|string|max:255',
            'prenom' => 'nullable|string|max:255',
            'telephone' => 'nullable|string|max:20',
            'photo' => 'nullable|String',
            'email' => 'nullable|string|email|max:255',
            'addresse' => 'nullable|string|max:255',
        ]);
    
        // Update common fields
        $user->fill($request->only(['nom', 'prenom', 'telephone', 'addresse']));
    
        if ($request->filled('photo')) {
            $user->photo = $request->photo;
        }
    
        // Update email only if provided
        if ($request->filled('email')) {
            $user->email = $request->email;
        }
    
        // Role-specific updates
        switch ($user->role) {
            case 'admin':
                $this->validateAdmin($request, $user);
                break;
            case 'vigile':
                $this->validateVigile($request, $user);
                break;
            case 'employe':
                $this->validateEmploye($request, $user);
                break;
            case 'etudiant':
                $this->validateEtudiant($request, $user);
                break;
            default:
                return response()->json(['error' => 'Invalid role'], 400);
        }
    
        // Save the updated user
        $user->save();
    
        return response()->json(['message' => 'User updated successfully', 'user' => User::find($user->id)], 200);
    }
    
    protected function validateAdmin(Request $request, User $user)
    {
        $request->validate([
            'cardId' => 'nullable|string|max:255|unique:users,cardId,' . $user->id,
            'password' => 'nullable|string|min:8',
        ]);
        $user->cardId = $request->cardId ?? $user->cardId;
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    }
    
    protected function validateVigile(Request $request, User $user)
    {
        $request->validate(['password' => 'nullable|string|min:8']);
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }
    }
    
    protected function validateEmploye(Request $request, User $user)
    {
        $request->validate([
            'departement' => 'nullable|string|max:255',
            'fonction' => 'nullable|string|max:255',
            'cardId' => 'nullable|string|max:255|unique:users,cardId,' . $user->id,
        ]);
        $user->departement = $request->departement ?? $user->departement;
        $user->fonction = $request->fonction ?? $user->fonction;
        $user->cardId = $request->cardId ?? $user->cardId;
    }
    
    protected function validateEtudiant(Request $request, User $user)
    {
        $request->validate([
            'cohorte' => 'nullable|string|max:255',
            'cardId' => 'nullable|string|max:255|unique:users,cardId,' . $user->id,
        ]);
        $user->cohorte = $request->cohorte ?? $user->cohorte;
        $user->cardId = $request->cardId ?? $user->cardId;
    }

     /**
     * Delete a single user by ID.
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteUser(string $id)
    {
        // Find the user by ID
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Delete the user
        $user->delete();

        return response()->json(['message' => 'User deleted successfully'], 200);
    }

    /**
     * Delete multiple users by their IDs.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteMultipleUsers(Request $request)
    {
        
        // Validate the request to ensure IDs are provided
          $request->validate([
            'ids' => 'required|array|min:1',
            'ids.*' => 'string', // Validate each ID format
        ]);

        // Find existing users
        $existingUsers = User::whereIn('_id', $request->ids)->pluck('_id')->toArray();

        // Determine non-existent IDs
        $nonExistentIds = array_diff($request->ids, $existingUsers);

        // Delete users with valid IDs
        $deletedCount = User::whereIn('_id', $existingUsers)->delete();

        // Prepare the response
        $response = [
            'deleted_count' => $deletedCount,
            'non_existent_ids' => $nonExistentIds,
        ];

        // Set the response status based on errors
        $status = empty($nonExistentIds) ? 200 : 207; // 207 Multi-Status for partial success

        return response()->json($response, $status);
    }
    
    /**
     * Export all users in CSV format.
     */
    public function importUsersFromCSV(Request $request)
    {
        // Validate the uploaded file
        $request->validate([
            'file' => 'required|file|mimes:csv,txt|max:2048',
        ]);

        // Open the file
        $file = fopen($request->file('file')->getRealPath(), 'r');


        // Read the header
        $header = fgetcsv($file);

        
       // Définir les colonnes attendues
        $expectedColumns = ['nom', 'prenom', 'telephone', 'email', 'addresse', 'role', 'cardId', 'password', 'departement', 'fonction', 'cohorte', 'photo', 'matricule'];

        // Vérifier les colonnes manquantes
        $missingColumns = array_diff($expectedColumns, $header);
        if (!empty($missingColumns)) {
            return response()->json(['error' => 'Format CSV invalide. Colonnes manquantes : ' . implode(', ', $missingColumns)], 400);
        }

        // Vérifier les colonnes supplémentaires
        $extraColumns = array_diff($header, $expectedColumns);
        if (!empty($extraColumns)) {
            return response()->json(['error' => 'Format CSV invalide. Colonnes supplémentaires : ' . implode(', ', $extraColumns)], 400);
        }

        // Initialize counters
        $createdUsers = [];
        $errors = [];

        // Process each row
        while (($row = fgetcsv($file)) !== false) {
            $data = array_combine($header, $row);

            // Validate role-specific fields
            $userData = [
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'telephone' => $data['telephone'],
                'email' => $data['email'],
                'addresse' => $data['addresse'],
                'role' => $data['role'],
                
            ];

            try {

                // Role-specific fields
                switch ($data['role']) {
                    case 'admin':
                        $userData['cardId'] = $data['cardId'];
                        $userData['password'] = Hash::make($data['password']);
                        $matricule = 'MAT_ADM-' . strtoupper(uniqid());
                        $userData['matricule'] = $matricule;

                        break;

                    case 'vigile':
                        $userData['password'] = Hash::make($data['password']);
                        $matricule = 'MAT_VIG-' . strtoupper(uniqid());
                        $userData['matricule'] = $matricule;
                        break;

                    case 'employe':
                        $userData['departement'] = $data['departement'];
                        $userData['fonction'] = $data['fonction'];
                        $userData['cardId'] = $data['cardId'];
                        $matricule = 'MAT_EMP-' . strtoupper(uniqid());
                        $userData['matricule'] = $matricule;
                        break;

                    case 'etudiant':
                        $userData['cohorte'] = $data['cohorte'];
                        $userData['cardId'] = $data['cardId'];
                        $matricule = 'MAT_ETU-' . strtoupper(uniqid());
                        $userData['matricule'] = $matricule;
                        break;

                    default:
                        throw new \Exception('Invalid role: ' . $data['role']);
                }

                

                // Create the user
                $createdUsers[] = User::create($userData);
            } catch (\Exception $e) {
                $errors[] = ['row' => $row, 'error' => $e->getMessage()];
            }
        }

        fclose($file);

        // Return response
        return response()->json([
            'message' => count($createdUsers) . ' users created successfully.',
            'errors' => $errors,
        ], count($errors) > 0 ? 207 : 200); // 207 Multi-Status for partial success
    }
    
    //fonction pour recupérer les users par départements
    public function getUsersByDepartment($departement)
    {
        $users = User::where('departement', $departement)->get();
        if (!$users) {
            return response()->json(['error' => 'No users found for this department'], 404);
        }
        return response()->json($users, 200);
    }

    //fonction pour recupérer les users par cohortes
    public function getUsersByCohorte($cohorte)
    {
        $users = User::where('cohorte', $cohorte)->get();
        if (!$users) {
            return response()->json(['error' => 'Aucun user trouvé'], 404);
        }
        return response()->json($users, 200);
    }

   
}
