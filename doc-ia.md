# Documentation du Chat IA

## Fonctionnement

Le chat IA utilise l'outil **OpenCode** pour générer des réponses intelligentes.

### Flux de données

1. **Entrée utilisateur** : L'utilisateur envoie un message via le formulaire
2. **Contrôleur** : `IaChatController` (`app/Http/Controllers/IaChat/IaChatController.php`) reçoit la requête
3. **Contexte DB** : Le contrôleur récupère les tables disponibles et les transmet à l'IA
4. **Traitement** : Le contrôleur appelle l'exécutable OpenCode avec le message + contexte DB
5. **Réponse** : Le résultat est nettoyé et affiché à l'utilisateur

### Accès à la base de données

L'IA peut maintenant interroger la base de données via un endpoint API :

1. **Route API** : `POST /api/ia-chat/query-database`
2. **Paramètre** : `query` (requête SQL)
3. **Contexte fourni** : Liste des tables disponibles dans la DB

### Code clé - generateAIResponse (ligne 362-405)

```php
private function generateAIResponse($message, $imagePath, $user)
{
    // Récupérer les tables disponibles pour donner le contexte à l'IA
    $tables = $this->getDatabaseTables();
    $dbContext = "\n\n[TABLES DISPONIBLES: " . implode(', ', $tables) . "]";
    
    // Instructions pour que l'IA puisse interroger la DB
    $dbInstructions = "\n\n[INSTRUCTIONS BASE DE DONNÉES]\n";
    $dbInstructions .= "Tu peux exécuter des requêtes SQL sur cette base de données MySQL.\n";
    $dbInstructions .= "Pour exécuter une requête, utilise l'endpoint: POST /api/ia-chat/query-database\n";
    $dbInstructions .= "Avec le paramètre 'query' contenant la requête SQL.\n";
    $dbInstructions .= "Exemple: await fetch('/api/ia-chat/query-database', {method: 'POST', body: JSON.stringify({query: 'SELECT * FROM users LIMIT 5'})});\n";
    $dbInstructions .= "La base de données s'appelle: generale\n";
    
    // Construire le message complet avec contexte DB
    $fullMessage = $message ?: 'Répons à ma question.';
    $fullMessage .= $dbContext . $dbInstructions;
    
    // Exécution de OpenCode
    $opencodeCmd = "/home/dydy/.opencode/bin/opencode run " . escapeshellarg($fullMessage);
    $output = shell_exec("cd " . escapeshellarg(base_path()) . " && " . $opencodeCmd . " 2>&1");
    
    // Nettoyage des codes ANSI
    $cleanOutput = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
    
    return ['message' => trim($cleanOutput), 'image_path' => null];
}
```

### Méthodes utilitaires pour la DB

| Méthode | Ligne | Description |
|---------|-------|-------------|
| `getDatabaseTables()` | 308-320 | Retourne la liste des tables disponibles |
| `getTableStructure($table)` | 332-346 | Retourne les colonnes d'une table |
| `queryDatabase($request)` | 269-306 | Endpoint API pour exécuter des requêtes SQL |

### Fichiers utilisés

| Fichier | Rôle |
|---------|------|
| `app/Http/Controllers/IaChat/IaChatController.php` | Contrôleur principal |
| `routes/api.php` | Route pour query-database |
| `resources/views/ia-chat/index.blade.php` | Vue du chat |

### Tables de base de données

- `ia_chat_conversations` : Stocke les conversations
- `ia_chat_messages` : Stocke les messages (role: user/assistant)

### Routage

Le chat est accessible différemment selon le type d'utilisateur :
- SuperAdmin : `super-admin.ia-chat`
- Admin : `admin.ia-chat`
- User : `role-dynamique.ia-chat`
- Client : `client.ia-chat`