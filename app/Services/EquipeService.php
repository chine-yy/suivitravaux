<?php

namespace App\Services;

use App\Models\Equipe;
use App\Models\Projet;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use App\Mail\NouvelleEquipeMail;
use App\Mail\UserAddedToEquipeMail;
use App\Mail\UserRemovedFromEquipeMail;
use App\Mail\RemovedFromChefRoleMail;

class EquipeService
{
    /**
     * Create a new team
     */
    public function createTeam(array $data)
    {
        $validator = Validator::make($data, [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'projet_id' => 'required|exists:projets,id',
            'chef_equipe_id' => 'required|exists:users,id',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ], [
            'nom.required' => 'Le nom de l\'équipe est obligatoire.',
            'projet_id.required' => 'Vous devez sélectionner un projet.',
            'chef_equipe_id.required' => 'Vous devez sélectionner un chef d\'équipe.',
            'users.required' => 'Vous devez sélectionner au moins un membre.',
            'users.min' => 'Vous devez sélectionner au moins un membre.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $projet = Projet::where('id', $data['projet_id'])->first();

        if (!$projet || $projet->statut === 'termine') {
            throw ValidationException::withMessages([
                'projet_id' => 'Le projet sélectionné n\'existe pas ou n\'est pas actif.',
            ]);
        }

        $equipe = Equipe::create([
            'nom' => $data['nom'],
            'description' => $data['description'],
            'projet_id' => $data['projet_id'],
            'chef_equipe_id' => $data['chef_equipe_id'],
            'statut' => $data['statut'] ?? 'active',
        ]);

        // Assurer que le chef d'équipe fait bien partie des membres
        if (!in_array($data['chef_equipe_id'], $data['users'])) {
            $data['users'][] = $data['chef_equipe_id'];
        }

        $equipe->users()->sync($data['users']);

        // Envoyer email au chef d'équipe
        $chef = User::find($data['chef_equipe_id']);
        if ($chef && $chef->email) {
            try {
                Mail::to($chef->email)->send(new NouvelleEquipeMail($chef, $equipe));
            } catch (\Exception $e) {
                Log::error("Erreur lors de l'envoi de l'email au chef d'équipe : " . $e->getMessage());
            }
        }

        // Envoyer email aux autres membres ajoutés
        $addedUserIds = array_diff($data['users'], [$data['chef_equipe_id']]);
        foreach ($addedUserIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new UserAddedToEquipeMail($user, $equipe));
                } catch (\Exception $e) {
                    Log::error("Erreur email ajout membre : " . $e->getMessage());
                }
            }
        }

        Log::info('Équipe créée', [
            'equipe_id' => $equipe->id,
            'nom' => $equipe->nom,
            'projet_id' => $equipe->projet_id,
            'admin_id' => auth()->id(),
            'role' => auth()->guard('superadmin')->check() ? 'superadmin' : 'admin',
        ]);

        return $equipe;
    }

    /**
     * Update an existing team
     */
    public function updateTeam(Equipe $equipe, array $data)
    {
        $validator = Validator::make($data, [
            'nom' => 'required|string|max:255',
            'description' => 'nullable|string',
            'projet_id' => 'required|exists:projets,id',
            'chef_equipe_id' => 'required|exists:users,id',
            'users' => 'required|array|min:1',
            'users.*' => 'exists:users,id',
        ], [
            'nom.required' => 'Le nom de l\'équipe est obligatoire.',
            'projet_id.required' => 'Vous devez sélectionner un projet.',
            'chef_equipe_id.required' => 'Vous devez sélectionner un chef d\'équipe.',
            'users.required' => 'Vous devez sélectionner au moins un membre.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $projet = Projet::where('id', $data['projet_id'])->first();

        if (!$projet || $projet->statut === 'termine') {
            throw ValidationException::withMessages([
                'projet_id' => 'Le projet sélectionné n\'existe pas ou n\'est plus actif.',
            ]);
        }

        $validator->validate();

        $oldUserIds = $equipe->users->pluck('id')->toArray();
        $oldChefId = $equipe->chef_equipe_id;

        $equipe->update([
            'nom' => $data['nom'],
            'description' => $data['description'],
            'projet_id' => $data['projet_id'],
            'chef_equipe_id' => $data['chef_equipe_id'],
            'statut' => $data['statut'] ?? $equipe->statut,
        ]);

        if (!in_array($data['chef_equipe_id'], $data['users'])) {
            $data['users'][] = $data['chef_equipe_id'];
        }

        $equipe->users()->sync($data['users']);

        $newUserIds = $data['users'];
        $addedUserIds = array_diff($newUserIds, $oldUserIds);
        $removedUserIds = array_diff($oldUserIds, $newUserIds);

        // Envoyer email au chef si changé
        $newChefId = $data['chef_equipe_id'];
        if ($oldChefId != $newChefId) {
            $newChef = User::find($newChefId);
            if ($newChef && $newChef->email) {
                try {
                    Mail::to($newChef->email)->send(new NouvelleEquipeMail($newChef, $equipe));
                } catch (\Exception $e) {
                    Log::error("Erreur email nouveau chef : " . $e->getMessage());
                }
            }

            if ($oldChefId) {
                $oldChef = User::find($oldChefId);
                if ($oldChef && $oldChef->email) {
                    try {
                        Mail::to($oldChef->email)->send(new RemovedFromChefRoleMail($oldChef, $equipe));
                    } catch (\Exception $e) {
                        Log::error("Erreur email ancien chef : " . $e->getMessage());
                    }
                }
            }

            // Ne pas envoyer "ajouté à l'équipe" s'il est déjà notifié comme chef
            $addedUserIds = array_diff($addedUserIds, [$newChefId]);
        }

        foreach ($addedUserIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new UserAddedToEquipeMail($user, $equipe));
                } catch (\Exception $e) {
                    Log::error("Erreur email ajout membre : " . $e->getMessage());
                }
            }
        }

        foreach ($removedUserIds as $userId) {
            $user = User::find($userId);
            if ($user && $user->email) {
                try {
                    Mail::to($user->email)->send(new UserRemovedFromEquipeMail($user, $equipe));
                } catch (\Exception $e) {
                    Log::error("Erreur email retrait membre : " . $e->getMessage());
                }
            }
        }

        Log::info('Équipe modifiée', [
            'equipe_id' => $equipe->id,
            'nom' => $equipe->nom,
            'admin_id' => auth()->id(),
        ]);

        return $equipe;
    }

    /**
     * Delete a team
     */
    public function deleteTeam(Equipe $equipe)
    {
        $equipe_id = $equipe->id;
        $nom = $equipe->nom;

        $equipe->users()->detach();
        $equipe->delete();

        Log::info('Équipe supprimée', [
            'equipe_id' => $equipe_id,
            'nom' => $nom,
            'admin_id' => auth()->id(),
        ]);

        return true;
    }

    /**
     * Get teams with filters
     */
    public function getTeams(array $filters = [])
    {
        $query = Equipe::with(['projet', 'users']);

        if (isset($filters['projet_id'])) {
            $query->where('projet_id', $filters['projet_id']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nom', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->latest()->paginate(10);
    }
}
