<?php
namespace App\Http\Controllers\IaChat;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Message;
use App\Http\Controllers\Controller;

/**
 * Contrôleur pour le Chat IA
 * Gère les conversations avec un assistant IA basé sur OpenCode
 */
class IaChatController extends Controller
{
    /**
     * Retourne l'utilisateur connecté et son type en se basant sur la route active.
     * Pour éviter les conflits si un utilisateur a plusieurs rôles, 
     * on se base strictement sur le préfixe de la route demandée.
     *
     * @return array ['user' => Model|null, 'type' => string|null]
     */
    private function resolveAuthContext(Request $request)
    {
        $routeName = $request->route()->getName() ?? '';

        if (str_starts_with($routeName, 'super-admin.')) {
            $user = Auth::guard('superadmin')->user() ?? Auth::user();
            return ['user' => $user, 'type' => 'SuperAdmin'];
        } 
        
        if (str_starts_with($routeName, 'partenaire.')) {
            $user = Auth::guard('partenaire')->user() ?? Auth::user();
            if ($user && ($user->role->nom ?? '') === 'Partenaire') {
                return ['user' => $user, 'type' => 'Partenaire'];
            }
        } 
        
        // Par défaut: role-dynamique ou admin-entreprise
        $user = Auth::guard('web')->user();
        if ($user) {
            $type = (method_exists($user, 'isAdminEntreprise') && $user->isAdminEntreprise()) ? 'Admin' : 'User';
            return ['user' => $user, 'type' => $type];
        }

        return ['user' => null, 'type' => null];
    }

    /**
     * Affiche la page principale du chat IA
     *
     * Récupère l'utilisateur connecté et son type (SuperAdmin, Admin, User, Partenaire)
     * Charge les conversations existantes et les messages si une conversation est sélectionnée
     *
     * @param Request $request Peut contenir conversation_id pour charger une conversation spécifique
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        // Détection du type d'utilisateur connecté selon le contexte URL
        $context = $this->resolveAuthContext($request);
        $user = $context['user'];
        $userType = $context['type'];

        // Redirection si aucun utilisateur connecté
        if (!$user) {
            return redirect()->route('login');
        }

        // Récupération des conversations de l'utilisateur
        $conversations = $this->getConversations($user, $userType);
        
        // Récupération des autorisations
        $autorisations = [];
        if ($userType === 'SuperAdmin') {
            $autorisations = $user->permissions()->pluck('slug')->toArray();
        } elseif ($user->role) {
            $autorisations = $user->role->permissions()->pluck('slug')->toArray();
        }

        // Initialisation des variables par défaut
        $selectedConversation = null;
        $messages = collect();

        // Si une conversation est sélectionnée, charger ses messages (avec vérification d'appartenance)
        if ($request->has('conversation_id')) {
            $selectedConversation = $request->conversation_id;
            
            // Sécurité: vérifier que la conversation appartient bien à l'utilisateur
            $conversation = DB::table('ia_chat_conversations')
                ->where('id', $selectedConversation)
                ->where('user_id', $user->id)
                ->where('user_type', $userType)
                ->first();

            if ($conversation) {
                $messages = DB::table('ia_chat_messages')
                    ->where('conversation_id', $selectedConversation)
                    ->orderBy('created_at', 'asc')
                    ->get();
            } else {
                $selectedConversation = null;
                $messages = collect();
            }
        }

        // Détermination du layout et de la route de base selon le type d'utilisateur
        $layout = $this->getLayout($userType);
        $baseRoute = $this->getBaseRoute($userType);

        // Sécurité: Rediriger si l'utilisateur accède à une route qui ne correspond pas à son type
        $currentRoute = $request->route()->getName();
        $expectedRoute = $baseRoute . '.index';

        if ($currentRoute !== $expectedRoute) {
            return redirect()->route($expectedRoute, $request->all());
        }

        return view('ia-chat.index', compact(
            'conversations',
            'selectedConversation',
            'messages',
            'layout',
            'baseRoute',
            'userType',
            'user'
        ));
    }

    /**
     * Envoie un message et reçoit une réponse de l'IA
     *
     * Cette méthode:
     * 1. Valide l'utilisateur connecté
     * 2. Crée une nouvelle conversation si nécessaire
     * 3. Sauvegarde le message utilisateur
     * 4. Appelle l'IA pour générer une réponse
     * 5. Sauvegarde la réponse de l'IA
     *
     * @param Request $request Contient: message, file (optionnel), conversation_id (optionnel)
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $context = $this->resolveAuthContext($request);
        $user = $context['user'];
        $userType = $context['type'];

        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        // Validation des données entrantes
        // Le message est requis sauf si un fichier est上传é
        $request->validate([
            'message' => 'required_without:file|string|nullable',
            'file' => 'nullable|image|max:20240', // Max 20MB pour les images
            'conversation_id' => 'nullable|integer'
        ]);

        $conversationId = $request->conversation_id;

        // Création d'une nouvelle conversation si aucune n'existe
        if (!$conversationId) {
            $conversationId = DB::table('ia_chat_conversations')->insertGetId([
                'user_id' => $user->id,
                'user_type' => $userType,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        } else {
            // Sécurité: vérifier que la conversation appartient bien à l'utilisateur
            $ownsConversation = DB::table('ia_chat_conversations')
                ->where('id', $conversationId)
                ->where('user_id', $user->id)
                ->where('user_type', $userType)
                ->exists();

            if (!$ownsConversation) {
                return response()->json(['error' => 'Conversation non trouvée ou accès refusé'], 403);
            }
        }

        // Traitement du fichier上传é (image)
        $filePath = null;
        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('ia-chat-uploads', 'public');
        }

        // Sauvegarde du message utilisateur en base de données
        $userMessageId = DB::table('ia_chat_messages')->insertGetId([
            'conversation_id' => $conversationId,
            'role' => 'user',
            'content' => $request->message ?? '',
            'image_path' => $filePath,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Appel de l'IA pour générer une réponse
        $response = $this->generateAIResponse($request->message ?? '', $filePath, $user);

        // Sauvegarde de la réponse de l'IA
        DB::table('ia_chat_messages')->insert([
            'conversation_id' => $conversationId,
            'role' => 'assistant',
            'content' => $response['message'],
            'image_path' => $response['image_path'] ?? null,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        // Mise à jour de la date de la conversation
        DB::table('ia_chat_conversations')
            ->where('id', $conversationId)
            ->update(['updated_at' => now()]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversationId,
            'response' => $response
        ]);
    }

    /**
     * Crée une nouvelle conversation vide
     *
     * Utile pour commencer une nouvelle discussion sans envoyer de message
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function createConversation(Request $request)
    {
        $context = $this->resolveAuthContext($request);
        $user = $context['user'];
        $userType = $context['type'];

        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        // Création de la conversation en base de données
        $conversationId = DB::table('ia_chat_conversations')->insertGetId([
            'user_id' => $user->id,
            'user_type' => $userType,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'conversation_id' => $conversationId
        ]);
    }

    /**
     * Supprime une conversation et tous ses messages
     *
     * Supprime d'abord les messages puis la conversation elle-même
     *
     * @param Request $request
     * @param int $id ID de la conversation à supprimer
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteConversation(Request $request, $id)
    {
        $context = $this->resolveAuthContext($request);
        $user = $context['user'];
        $userType = $context['type'];

        if (!$user) {
            return response()->json(['error' => 'Non autorisé'], 401);
        }

        // Sécurité: vérifier que la conversation appartient bien à l'utilisateur ET au contexte
        $conversation = DB::table('ia_chat_conversations')
            ->where('id', $id)
            ->where('user_id', $user->id)
            ->where('user_type', $userType)
            ->first();

        if (!$conversation) {
            return response()->json(['error' => 'Conversation non trouvée ou accès refusé'], 403);
        }

        // Suppression des messages de la conversation
        DB::table('ia_chat_messages')->where('conversation_id', $id)->delete();
        // Suppression de la conversation
        DB::table('ia_chat_conversations')->where('id', $id)->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Permet d'exécuter des requêtes SQL directes sur la base de données
     * 
     * Cette méthode est appelée par l'IA lorsqu'elle a besoin de données de la DB.
     * Elle peut être invoquée via:
     * 1. Requête API directe (query parameter)
     * 2. Via la méthode generateAIResponse qui détecte les besoins DB
     * 
     * @param Request $request Contient la requête SQL à exécuter
     * @return \Illuminate\Http\JsonResponse
     */
    public function queryDatabase(Request $request)
    {
        // Allow local requests without authentication (for AI on server)
        $isLocal = in_array($request->ip(), ['127.0.0.1', '::1']);
        
        if (!$isLocal) {
            $context = $this->resolveAuthContext($request);
            $user = $context['user'];

            if (!$user) {
                return response()->json(['error' => 'Non autorisé'], 401);
            }
        }

        // Validation de la requête
        $request->validate([
            'query' => 'required|string'
        ]);

        try {
            // Exécution de la requête SQL
            $queryString = $request->input('query');
            $results = DB::select($queryString);
            return response()->json([
                'success' => true,
                'results' => $results,
                'count' => count($results)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 400);
        }
    }

    /**
     * Extrait les tables disponibles dans la base de données
     * Utile pour que l'IA connaisse la structure de la DB
     * 
     * @return array Liste des tables
     */
    private function getDatabaseTables()
    {
        try {
            $tables = DB::select('SHOW TABLES');
            $tableNames = [];
            foreach ($tables as $table) {
                $tableArray = (array) $table;
                $tableNames[] = array_values($tableArray)[0];
            }
            return $tableNames;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Retourne la structure d'une table (colonnes et types)
     * 
     * @param string $tableName Nom de la table
     * @return array Structure de la table
     */
    private function getTableStructure($tableName)
    {
        try {
            $columns = DB::select("DESCRIBE " . $tableName);
            return $columns;
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Génère une réponse de l'IA en utilisant OpenCode
     *
     * Cette méthode:
     * 1. Prépare la commande shell pour exécuter opencode
     * 2. Ajoute le chemin de l'image si uploadée
     * 3. Ajoute le contexte de la base de données (tables disponibles)
     * 4. Exécute la commande depuis le répertoire du projet Laravel
     * 5. Nettoie la sortie des codes ANSI (couleurs terminal)
     *
     * @param string $message Le message de l'utilisateur
     * @param string|null $imagePath Chemin de l'image uploadée (si existante)
     * @param mixed $user L'utilisateur connecté (pour contexte)
     * @return array ['message' => string, 'image_path' => string|null]
     */
    private function generateAIResponse($message, $imagePath, $user)
    {
        // Précharger les données de la base pour éviter les appels HTTP par l'IA
        $dbData = $this->getDatabaseDataContext();
        
        $fullMessage = $message ?: 'Répons à ma question.';
        $fullMessage .= "\n\n[CONTENU DE LA BASE DE DONNÉES]\n" . $dbData;
        $fullMessage .= "\n(Réponds directement à partir de ces données, sans chercher à interroger la base toi-même.)";
        
        $prompt = escapeshellarg($fullMessage);
        
        // Construction de la commande OpenCode
        $opencodeCmd = "/home/dydy/.opencode/bin/opencode run " . $prompt;
        
        // Ajout du paramètre image si une image est présente
        if ($imagePath) {
            $fullImagePath = storage_path('app/public/' . $imagePath);
            $opencodeCmd .= " -f " . escapeshellarg($fullImagePath);
        }

        // Exécution de la commande depuis le répertoire du projet pour accéder au .env
        $projectPath = base_path();
        $output = shell_exec("cd " . escapeshellarg($projectPath) . " && " . $opencodeCmd . " 2>&1");
        
        // Nettoyage des codes de couleur ANSI (escape sequences)
        $cleanOutput = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
        // Suppression des lignes de build
        $cleanOutput = preg_replace('/> build .*\n/', '', $cleanOutput);
        // Suppression des lignes de commandes shell (php artisan, curl, $, etc.)
        $cleanOutput = preg_replace('/^\$ .*\n?/m', '', $cleanOutput);
        $cleanOutput = preg_replace('/^(php|curl|wget|npm|composer|node|python|artisan) .*\n?/m', '', $cleanOutput);
        // Suppression des sorties d'erreur (stderr) comme 2>/dev/null, || echo "..."
        $cleanOutput = preg_replace('/\|\| echo "[^"]*"/', '', $cleanOutput);
        $cleanOutput = preg_replace('/2>[^|>]*/', '', $cleanOutput);

        return [
            'message' => trim($cleanOutput) ?: "Je suis l'assistant IA. (Aucune réponse de l'exécutable OpenCode)",
            'image_path' => null
        ];
    }

    /**
     * Précharge les données de toutes les tables de la base
     * pour que l'IA réponde directement sans faire d'appels HTTP.
     */
    private function getDatabaseDataContext()
    {
        try {
            $tables = $this->getDatabaseTables();
            if (empty($tables)) return 'Aucune table trouvée.';

            $context = '';
            $totalSize = 0;
            foreach ($tables as $table) {
                $count = DB::table($table)->count();
                $context .= "\nTable: $table ($count enregistrements)\n";

                $columns = DB::select("DESCRIBE `$table`");
                $colNames = array_map(fn($c) => $c->Field, $columns);
                $context .= "  Colonnes: " . implode(', ', $colNames) . "\n";

                if ($count > 0) {
                    $rows = DB::select("SELECT * FROM `$table` LIMIT 3");
                    foreach ($rows as $row) {
                        $line = "  - " . json_encode((array)$row, JSON_UNESCAPED_UNICODE) . "\n";
                        $context .= $line;
                    }
                }

                $totalSize += strlen($context);
                if ($totalSize > 15000) {
                    $context .= "\n... (autres tables tronquées pour la performance)";
                    break;
                }
            }
            return $context;
        } catch (\Exception $e) {
            return 'Erreur lors de la récupération des données: ' . $e->getMessage();
        }
    }

    /**
     * Détermine le type d'utilisateur à partir de l'objet user
     *
     * @param mixed $user L'objet utilisateur
     * @return string Type d'utilisateur: SuperAdmin, Admin, User, Partenaire ou Unknown
     */
    private function getUserType($user)
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return 'SuperAdmin';
        } elseif (method_exists($user, 'isAdminEntreprise') && $user->isAdminEntreprise()) {
            return 'Admin';
        } elseif ($user->role && $user->role->nom === 'Partenaire') {
            return 'Partenaire';
        }
        return 'User';
    }

    /**
     * Récupère toutes les conversations d'un utilisateur
     *
     * Pour chaque conversation, récupère aussi le dernier message
     * pour l'afficher dans la liste latérale
     *
     * @param mixed $user L'utilisateur connecté
     * @param string $userType Le type d'utilisateur (SuperAdmin, Admin, User, Partenaire)
     * @return \Illuminate\Support\Collection
     */
    private function getConversations($user, $userType)
    {
        // Requête principale: récupérer les conversations de l'utilisateur
        $conversations = DB::table('ia_chat_conversations')
            ->where('user_id', $user->id)
            ->where('user_type', $userType)
            ->orderBy('updated_at', 'desc')
            ->get();

        // Pour chaque conversation, récupérer le dernier message pour l'aperçu
        return $conversations->map(function ($conv) {
            $lastMessage = DB::table('ia_chat_messages')
                ->where('conversation_id', $conv->id)
                ->orderBy('created_at', 'desc')
                ->first();

            return (object) [
                'id' => $conv->id,
                // Affiche les 50 premiers caractères du dernier message ou "Nouvelle conversation"
                'preview' => $lastMessage ? substr($lastMessage->content ?? '', 0, 50) : 'Nouvelle conversation',
                'updated_at' => $conv->updated_at
            ];
        });
    }

    /**
     * Retourne le layout à utiliser selon le type d'utilisateur
     *
     * Chaque type d'utilisateur a son propre layout avec sa propre navigation
     *
     * @param string $userType Type d'utilisateur
     * @return string Nom du layout à utiliser
     */
    private function getLayout($userType)
    {
        return match($userType) {
            'SuperAdmin' => 'layouts.super-admin',
            'Admin' => 'layouts.role-dynamique',
            'User' => 'layouts.role-dynamique',
            'Partenaire' => 'layouts.partenaire',
            default => 'layouts.role-dynamique'
        };
    }

    /**
     * Retourne le prefix de route selon le type d'utilisateur
     *
     * Utilisé pour générer les URLs correctes pour chaque type d'utilisateur
     *
     * @param string $userType Type d'utilisateur
     * @return string Prefix de route
     */
    private function getBaseRoute($userType)
    {
        return match($userType) {
            'SuperAdmin' => 'super-admin.ia-chat',
            'Admin' => 'role-dynamique.ia-chat',
            'User' => 'role-dynamique.ia-chat',
            'Partenaire' => 'partenaire.ia-chat',
            default => 'role-dynamique.ia-chat'
        };
    }
}
