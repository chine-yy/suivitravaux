<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Message;
use App\Models\User;

use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Get the current authenticated user and its type based on active session.
     */
    private function getCurrentUser(Request $request)
    {
        $routeName = $request->route()->getName() ?? '';

        if (str_starts_with($routeName, 'super-admin.')) {
            $user = Auth::guard('superadmin')->user() ?? Auth::user();
            if ($user) {
                return ['id' => $user->id, 'type' => 'SuperAdmin', 'guard' => 'superadmin', 'model' => $user];
            }
        }

        if (str_starts_with($routeName, 'partenaire.')) {
            $user = Auth::guard('partenaire')->user() ?? Auth::user();
            if ($user && ($user->role->nom ?? '') === 'Partenaire') {
                return ['id' => $user->id, 'type' => 'Partenaire', 'guard' => 'partenaire', 'model' => $user];
            }
        }

        // Par défaut: role-dynamique ou admin-entreprise
        $user = Auth::guard('web')->user();
        if ($user) {
            // Is it a true Admin "Administrateur Entreprise" or just a dynamic role?
            $type = ($user->role && preg_match('/Administrateur Entreprise/i', $user->role->nom)) ? 'Admin' : 'User';
            return ['id' => $user->id, 'type' => $type, 'guard' => 'web', 'model' => $user];
        }

        return null;
    }

    public function index(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);

        if (!$currentUser) {
            return redirect('/login')->with('error', 'Veuillez vous connecter pour accéder à la messagerie.');
        }

        // Available contacts logically depend on the roles
        $contacts = $this->getAvailableContacts($currentUser);

        $selectedContactId = $request->get('id');
        $selectedContactType = $request->get('type');
        $selectedContact = null;
        $selectedContactMeta = null;
        $messages = [];

        if ($selectedContactId && $selectedContactType) {
            $selectedContact = $this->getContactModel($selectedContactType, $selectedContactId);
            foreach ($contacts as $c) {
                if ($c['type'] === $selectedContactType && (int) $c['model']->id === (int) $selectedContactId) {
                    $selectedContactMeta = $c;
                    break;
                }
            }

            // Security: Check if this contact is in available contacts
            $isAuthorized = false;
            foreach ($contacts as $c) {
                if ($c['type'] === $selectedContactType && $c['model']->id == $selectedContactId) {
                    $isAuthorized = true;
                    break;
                }
            }

            if ($selectedContact && $isAuthorized) {
                // Fetch discussion messages
                $messages = Message::where(function ($q) use ($currentUser, $selectedContactId, $selectedContactType) {
                    $q->where('sender_id', $currentUser['id'])
                        ->where('sender_type', $currentUser['type'])
                        ->where('receiver_id', $selectedContactId)
                        ->where('receiver_type', $selectedContactType);
                })
                    ->orWhere(function ($q) use ($currentUser, $selectedContactId, $selectedContactType) {
                        $q->where('sender_id', $selectedContactId)
                            ->where('sender_type', $selectedContactType)
                            ->where('receiver_id', $currentUser['id'])
                            ->where('receiver_type', $currentUser['type']);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mark messages as read
                Message::where('sender_id', $selectedContactId)
                    ->where('sender_type', $selectedContactType)
                    ->where('receiver_id', $currentUser['id'])
                    ->where('receiver_type', $currentUser['type'])
                    ->update(['is_read' => true]);
            }
        }

        if ($request->ajax() && $request->has('last_id')) {
            $newMessages = [];
            if ($selectedContactId && $selectedContactType) {
                $newMessages = Message::where('id', '>', $request->last_id)
                    ->where(function ($q) use ($currentUser, $selectedContactId, $selectedContactType) {
                        $q->where('sender_id', $selectedContactId)
                            ->where('sender_type', $selectedContactType)
                            ->where('receiver_id', $currentUser['id'])
                            ->where('receiver_type', $currentUser['type']);
                    })
                    ->orderBy('created_at', 'asc')
                    ->get();

                // Mark new ones as read
                if ($newMessages->count() > 0) {
                    Message::whereIn('id', $newMessages->pluck('id'))
                        ->update(['is_read' => true]);
                }
            }
            return response()->json(['messages' => $newMessages]);
        }

        return view('chat.index', compact('currentUser', 'contacts', 'selectedContact', 'selectedContactMeta', 'messages'));
    }

    /**
     * Store a newly created message.
     */
    public function store(Request $request)
    {
        $currentUser = $this->getCurrentUser($request);
        if (!$currentUser) {
            return response()->json(['error' => 'Non authentifié'], 401);
        }

        $request->validate([
            'receiver_id' => 'required|integer',
            'receiver_type' => 'required|string|in:SuperAdmin,Admin,User,Partenaire',
            'message' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120',
            'audio' => 'nullable|mimetypes:audio/mpeg,audio/wav,audio/ogg,audio/webm,video/webm,audio/x-m4a,audio/mp4,audio/mpeg,audio/mpga,video/ogg,application/octet-stream|max:10240',
        ]);

        // Security: Check if receiver is in available contacts
        $contacts = $this->getAvailableContacts($currentUser);
        $isAuthorized = false;
        foreach ($contacts as $c) {
            if ($c['type'] === $request->receiver_type && $c['model']->id == $request->receiver_id) {
                $isAuthorized = true;
                break;
            }
        }

        if (!$isAuthorized) {
            return response()->json(['error' => 'Vous n\'êtes pas autorisé à envoyer un message à ce destinataire.'], 403);
        }

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('chat/images', 'public');
        }

        $audioPath = null;
        if ($request->hasFile('audio')) {
            $audioPath = $request->file('audio')->store('chat/audio', 'public');
        }

        if (!$request->message && !$imagePath && !$audioPath) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Le message ne peut pas être vide.'], 422);
            }
            return redirect()->back()->with('error', 'Le message ne peut pas être vide.');
        }

        $message = Message::create([
            'sender_id' => $currentUser['id'],
            'sender_type' => $currentUser['type'],
            'receiver_id' => $request->receiver_id,
            'receiver_type' => $request->receiver_type,
            'message' => $request->message,
            'image_path' => $imagePath,
            'audio_path' => $audioPath,
            'is_read' => false,
        ]);

        if ($request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message
            ]);
        }

        return redirect()->back();
    }

    /**
     * Get all possible contacts based on current user's role and permissions.
     */
    private function getAvailableContacts($currentUser)
    {
        $contacts = [];
        $user = $currentUser['model'];

        $canActiver = $user->hasPermission('chat-messagerie-activer');

        // Les Super Admin ont tous les droits
        if ($currentUser['type'] === 'SuperAdmin') {
            $canActiver = true;
        }

        if (!$canActiver) {
            return [];
        }

        $currentUserId = $currentUser['id'];

        // Avec la permission Activer, on affiche TOUJOURS le Super Admin
        $superadmin = User::whereHas('role', fn($q) => $q->where('nom', 'Super Admin'))->first();
        if ($superadmin && $superadmin->id !== $currentUserId) {
            $contacts[] = ['model' => $superadmin, 'type' => 'SuperAdmin', 'label' => 'Super Admin'];
        }

        // Ajouter TOUJOURS l'Administrateur Entreprise pour tous les utilisateurs
        if ($currentUser['type'] === 'SuperAdmin') {
            $admins = User::whereHas('role', fn($q) => $q->where('nom', 'LIKE', '%Administrateur Entreprise%'))->get();
            foreach ($admins as $admin) {
                if ($admin->id !== $currentUserId) {
                    $contacts[] = ['model' => $admin, 'type' => 'Admin', 'label' => 'Admin Entreprise'];
                }
            }
        } elseif ($currentUser['type'] === 'Admin') {
            $entrepriseId = $user->entreprise_id ?? null;
            if ($entrepriseId) {
                $admins = User::where('entreprise_id', $entrepriseId)
                    ->whereHas('role', fn($q) => $q->where('nom', 'LIKE', '%Administrateur Entreprise%'))
                    ->where('id', '!=', $currentUserId)
                    ->get();
                foreach ($admins as $admin) {
                    $contacts[] = ['model' => $admin, 'type' => 'Admin', 'label' => 'Admin Entreprise'];
                }
            }
        } else {
            // Pour les utilisateurs standards, ajouter l'Administrateur de leur entreprise
            if ($user->entreprise_id) {
                $admins = User::where('entreprise_id', $user->entreprise_id)
                    ->whereHas('role', fn($q) => $q->where('nom', 'LIKE', '%Administrateur Entreprise%'))
                    ->where('id', '!=', $currentUserId)
                    ->get();
                foreach ($admins as $admin) {
                    $contacts[] = ['model' => $admin, 'type' => 'Admin', 'label' => 'Administrateur Entreprise'];
                }
            }
        }

        // Récupérer les projets de l'utilisateur
        $projectIds = collect();

        if ($user->projet_id) {
            $projectIds->push($user->projet_id);
        }

        $projectIds = $projectIds->merge(
            $user->equipes()->pluck('projet_id')
        )->unique()->filter();

        // Ajouter les acteurs du projet (utilisateurs non Super Admin, non Admin Entreprise, non Partenaire)
        $actorUserIds = collect();
        if ($projectIds->isNotEmpty()) {
            $actorUserIds = User::whereIn('projet_id', $projectIds)
                ->where('id', '!=', $currentUserId)
                ->whereHas('role', fn($q) => $q->whereNotIn('nom', ['Super Admin', 'Administrateur Entreprise', 'Partenaire']))
                ->pluck('id');

            // Ajouter aussi tout le monde qui est dans la même équipe
            $equipeActorIds = \App\Models\Equipe::whereHas('users', fn($q) => $q->where('user_id', $currentUserId))
                ->with('users')
                ->get()
                ->flatMap(fn($e) => $e->users->pluck('id'))
                ->unique()
                ->diff([$currentUserId]);

            $actorUserIds = $actorUserIds->merge($equipeActorIds)->unique();
        }

        if ($currentUser['type'] === 'SuperAdmin') {
            $actorUserIds = User::whereDoesntHave('role', fn($q) => $q->whereIn('nom', ['Super Admin', 'Administrateur Entreprise', 'Partenaire']))
                ->where('id', '!=', $currentUserId)
                ->pluck('id');
        }

        $actors = User::whereIn('id', $actorUserIds)->get();
        foreach ($actors as $actor) {
            $roleName = $actor->role->nom ?? 'Utilisateur';
            $contacts[] = ['model' => $actor, 'type' => 'User', 'label' => $roleName];
        }

        // Ajouter les partenaires: on affiche les partenaires rattachés aux projets des équipes si on en fait partie, ou tous en SuperAdmin.
        $partenaireIds = collect();
        if ($currentUser['type'] === 'SuperAdmin') {
            $partenaireIds = \App\Models\Partenaire::pluck('id');
        } elseif ($projectIds->isNotEmpty()) {
            // Partenaires via projet_id direct
            $partenaireIds = \App\Models\Partenaire::whereIn('projet_id', $projectIds)->pluck('id');
            // Partenaires via la table pivot projet_partenaires
            $pivotPartenaireIds = \Illuminate\Support\Facades\DB::table('projet_partenaires')
                ->whereIn('projet_id', $projectIds)
                ->pluck('user_id');
            $partenaireIds = $partenaireIds->merge($pivotPartenaireIds)->unique();
        }

        $partenaires = \App\Models\Partenaire::whereIn('id', $partenaireIds)->get();
        foreach ($partenaires as $partenaire) {
            $contacts[] = ['model' => $partenaire, 'type' => 'Partenaire', 'label' => 'Partenaire'];
        }

        foreach ($contacts as &$contact) {
            $contact['unread_count'] = Message::where('sender_id', $contact['model']->id)
                ->where('sender_type', $contact['type'])
                ->where('receiver_id', $currentUser['id'])
                ->where('receiver_type', $currentUser['type'])
                ->where('is_read', false)
                ->count();

            $fullname = trim(($contact['model']->prenom ?? '') . ' ' . ($contact['model']->nom ?? ''));
            if (!$fullname) {
                $fullname = $contact['model']->name ?? $contact['model']->email ?? 'Inconnu';
            }
            $contact['fullname'] = $fullname;

            if ($contact['type'] === 'Admin' && empty($contact['label'])) {
                $contact['label'] = $contact['model']->poste ?? 'Administrateur';
            } elseif ($contact['type'] === 'SuperAdmin' && empty($contact['label'])) {
                $contact['label'] = 'Super Admin';
            } elseif ($contact['type'] === 'User' && empty($contact['label'])) {
                $contact['label'] = $contact['model']->role->nom ?? 'Utilisateur';
            } elseif ($contact['type'] === 'Partenaire' && empty($contact['label'])) {
                $contact['label'] = 'Partenaire';
            }
        }

        $contacts = array_filter($contacts, function ($contact) use ($currentUser) {
            if ($contact['model'] === null)
                return false;
            if ($contact['model']->id == $currentUser['id'] && get_class($contact['model']) === get_class($currentUser['model'])) {
                return false;
            }
            return true;
        });

        $contacts = array_values($contacts);

        usort($contacts, function ($a, $b) {
            if ($a['unread_count'] !== $b['unread_count']) {
                return $b['unread_count'] - $a['unread_count'];
            }
            return strcmp($a['fullname'], $b['fullname']);
        });

        return $contacts;
    }

    private function getContactModel($type, $id)
    {
        switch ($type) {
            case 'User':
                return User::find($id);
            case 'Admin':
                return User::whereHas('role', fn($q) => $q->where('nom', 'LIKE', '%Administrateur Entreprise%'))->find($id);
            case 'SuperAdmin':
                return User::whereHas('role', fn($q) => $q->where('nom', 'Super Admin'))->find($id);
            case 'Partenaire':
                return \App\Models\Partenaire::find($id);
            default:
                return null;
        }
    }
}
