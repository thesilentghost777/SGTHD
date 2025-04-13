<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Http\Client\RequestException;
use InvalidArgumentException;
use RuntimeException;

class AIQueryService
{
    private string $apiKey;
    private string $apiEndpoint = 'https://api.openai.com/v1/chat/completions';
    private array $conversationHistory = [];
    private const MAX_RETRIES = 3;
    private const RETRY_DELAY = 100;
    private const TIMEOUT = 30;
    private const MAX_RESULTS = 100;
    private const CACHE_DURATION = 1440; // 24 hours in minutes

    public function __construct()
    {
        $this->apiKey = config('services.openai.api_key');
        if (empty($this->apiKey)) {
            Log::error('OpenAI API key missing');
            throw new RuntimeException('OpenAI API key is not configured');
        }
    }

    private function initializeConversation(): void
    {
        $schema = $this->getDatabaseSchema2();
        if (empty($schema)) {
            throw new RuntimeException('Failed to initialize database schema');
        }

        $this->conversationHistory = [[
            'role' => 'system',
            'content' => $this->buildSystemPrompt($schema)
        ]];
    }

    private function buildSystemPrompt(string $schema): string
    {
        return "Tu es un assistant spécialisé en bases de données MySQL. " .
               "Schéma de la base de données : {$schema}\n" .
               "Règles pour les requêtes SQL:\n" .
               "- Uniquement des requêtes commençant par SELECT\n" .
               "- Recherche avec LIKE et wildcards (%)\n" .
               "- Joins avec alias explicites\n" .
               "- Limite de " . self::MAX_RESULTS . " résultats\n" .
               "- Pas de requêtes de modification\n" .
               "- Ne jamais renvoyer les champs created_at, updated_at, email_verified_at,code_secret, password et remember_token sauf pour timeseries ou demande explicite : ce n'est pas grave si ta requete est longue\n" .
               "- Toujours privilégier les données lisibles par l'humain (noms plutôt que IDs)\n" .
               "- Pour les timeseries il faut toujours retourner les donnees avec les pseudo date et value :: donc par exemple si la table contient date_vente et qte_vendu , tu les retournera sous le pseudo date et value\n" .
               "- Pour les pie il faut toujours retourner les donnees avec les pseudo label et value \n" .
               "- fait attention au requete : quand il y'a un group by tous les champs concerner doivent etre pris en compte et n'utilise que les tables et collones qui existe dans la BD \n" .
               "- Faire des JOINs pour obtenir les informations descriptives plutôt que les IDs\n" .
               "- ma base de donnees est MySQl donc utilises seulement les elements disponible dans ce cas ex : CURDATE() au lieu de Current_date\n" .
               "- Ta réponse doit TOUJOURS être en deux parties séparées par un => :\n" .
               "  1. La requête SQL pure\n" .
               "  2. Le type de données qui sera retourner (float, integer, date, string, boolean, emptyArray, timeSeries, simpleArray, multiArray,  geographical, hierarchical, network, matrix, pie, unknown)\n" .
               "- 3. considere toujours les alias de lettre de debut different lorsque tu veux en utiliser pour deux tables ayant les meme lettre de debut : au lieu de faire ceci SELECT u.name, SUM(u.quantite_produit) AS total_quantite FROM Utilisation u JOIN users us ON u.producteur = us.id GROUP BY u.producteur, us.name ORDER BY total_quantite DESC LIMIT 2 qui est une requete erroner car u.name n'existe pas (c'est plutot us.name) cette erreur qui est du a l'alias tu peut faire users as u et Utilisation as x\n " .
               "a chaque fois que tu auras a faire au calcul de la production , quantite , benefice , cout des matieres ,  utilise la logique de celui si \n" .
               "public function produit_par_lot() { return collect(DB::table('Utilisation')->join('Produit_fixes', 'Utilisation.produit', '=', 'Produit_fixes.code_produit')->join('Matiere', 'Utilisation.matierep', '=', 'Matiere.id')->select('Utilisation.id_lot', 'Produit_fixes.nom as nom_produit', 'Produit_fixes.prix as prix_produit', 'Utilisation.quantite_produit', 'Matiere.nom as nom_matiere', 'Matiere.prix_par_unite_minimale', 'Utilisation.quantite_matiere', 'Utilisation.unite_matiere')->orderBy('Utilisation.id_lot')->get())->reduce(function(\$p, \$u) { \$idLot = \$u->id_lot; if (!isset(\$p[\$idLot])) \$p[\$idLot] = ['produit' => \$u->nom_produit, 'quantite_produit' => \$u->quantite_produit, 'prix_unitaire' => \$u->prix_produit, 'matieres' => [], 'valeur_production' => \$u->quantite_produit * \$u->prix_produit, 'cout_matieres' => 0]; \$p[\$idLot]['matieres'][] = ['nom' => \$u->nom_matiere, 'quantite' => \$u->quantite_matiere, 'unite' => \$u->unite_matiere, 'cout' => \$u->quantite_matiere * \$u->prix_par_unite_minimale]; \$p[\$idLot]['cout_matieres'] += \$u->quantite_matiere * \$u->prix_par_unite_minimale; return \$p; }, []); }\n" .
               "voici ces principaux prise en compte : Récupère les lots uniques pour éviter de compter deux fois la même productionCalcule correctement le total par produit\n" .
               $this->getDataTypeExamples();
    }

    private function getDataTypeExamples(): string
    {
        return "exemple de type pour te guider\n" .
               " -SELECT * FROM users LIMIT 3;\n" .
               " -\$dataType = simpleArray\n" .
               "Exemple de réponse:\n" .
               "SELECT e.nom, e.prenom FROM employees e WHERE e.id = 1 => string";
    }

    private function getDatabaseSchema2(): string
    {
        try {
            $schema = [
                'users' => [
                    'name' => 'string',
                    'email' => 'string',
                    'password' => 'string',
                    'date_naissance' => 'date',
                    'code_secret' => 'string',
                    'secteur' => 'string',
                    'role' => 'string',
                    'num_tel' => 'string',
                    'avance_salaire' => 'decimal:2',
                    'annee_debut_service' => 'integer'
                ],
                'Utilisation' => [
    'id' => 'bigInteger, primary',
    'id_lot' => 'string, length:20, index',
    'produit' => 'bigInteger, foreign:Produit_fixes, references:code_produit',
    'matierep' => 'bigInteger, foreign:Matiere, references:id',
    'producteur' => 'bigInteger, foreign:users, references:id',
    'quantite_produit' => 'decimal, precision:10, scale:2',
    'quantite_matiere' => 'decimal, precision:10, scale:3',
    'unite_matiere' => 'string',
    'created_at' => 'timestamp, nullable',
    'updated_at' => 'timestamp, nullable',
],



            ];
            return json_encode($schema, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);

        } catch (\JsonException $e) {
            Log::error('Failed to encode database schema', ['error' => $e->getMessage()]);
            throw new RuntimeException('Failed to encode database schema');
        }
    }


    public function generateSqlQuery(string $userQuery): array
{
    // Log pour indiquer le début de la génération de la requête SQL
    Log::info('Début de la génération de la requête SQL', ['user_query' => $userQuery]);

    $userQuery = trim($userQuery);
    if (empty($userQuery)) {
        Log::error('User query is empty', ['user_query' => $userQuery]);
        throw new InvalidArgumentException('User query cannot be empty');
    }

    // Log pour indiquer l'initialisation de la conversation
    Log::info('Initialisation de la conversation avec OpenAI');
    $this->initializeConversation();

    try {
        // Log pour indiquer la validation de la connexion à OpenAI
        Log::info('Validation de la connexion à OpenAI');
        $this->validateOpenAIConnection();

        // Ajout de la requête utilisateur à l'historique de conversation
        $this->conversationHistory[] = [
            'role' => 'user',
            'content' => "Génère une requête SQL pour : '{$userQuery}'. N'oublie pas de séparer ta réponse en deux parties avec un => comme expliqué. et Ne jamais renvoyer les champs created_at, updated_at, email_verified_at,code_secret, password et remember_token sauf pour timeseries ou demande explicite : ce n'est pas grave si ta requete est longue\n"
        ];

        // Log pour indiquer l'envoi de la requête à OpenAI
        Log::info('Envoi de la requête à OpenAI', ['user_query' => $userQuery]);
        $response = $this->makeOpenAIRequest();

        // Log pour indiquer la réception de la réponse d'OpenAI
        Log::info('Réponse reçue d\'OpenAI', ['response' => $response]);
        $aiResponse = $this->extractAIResponse($response);

        // Ajout de la réponse d'OpenAI à l'historique de conversation
        $this->conversationHistory[] = [
            'role' => 'assistant',
            'content' => $aiResponse
        ];

        // Log pour indiquer le traitement de la réponse d'OpenAI
        Log::info('Traitement de la réponse d\'OpenAI', ['ai_response' => $aiResponse]);
        $processedResponse = $this->processAIResponse($aiResponse);

        // Log pour indiquer la fin de la génération de la requête SQL
        Log::info('Requête SQL générée avec succès', ['processed_response' => $processedResponse]);
        return $processedResponse;

    } catch (\Exception $e) {
        // Log pour capturer les exceptions pendant la génération de la requête SQL
        Log::error('Exception pendant la génération de la requête SQL', [
            'message' => $e->getMessage(),
            'user_query' => $userQuery,
            'trace' => $e->getTraceAsString()
        ]);
        throw new RuntimeException('An error occurred during SQL query generation: ' . $e->getMessage());
    }
}

    private function validateOpenAIConnection(): void
    {
        $connectionTest = @fsockopen("api.openai.com", 443, $errno, $errstr, 5);
        if (!$connectionTest) {
            throw new RuntimeException("Unable to connect to OpenAI (port 443): $errstr");
        }
        fclose($connectionTest);
    }

    private function makeOpenAIRequest(): array
    {
        try {
            $response = Http::timeout(self::TIMEOUT)
                ->retry(self::MAX_RETRIES, self::RETRY_DELAY)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey
                ])
                ->post($this->apiEndpoint, [
                    'model' => 'gpt-4o-mini',
                    'messages' => $this->conversationHistory,
                    'temperature' => 0.3,
                ]);

            if ($response->failed()) {
                throw new RequestException($response);
            }

            return $response->json();
        } catch (RequestException $e) {
            throw new RuntimeException('OpenAI API error: ' . $e->getMessage());
        }
    }

    private function extractAIResponse(array $responseData): string
    {
        if (!isset($responseData['choices'][0]['message']['content'])) {
            throw new RuntimeException('Malformed OpenAI API response');
        }

        return trim($responseData['choices'][0]['message']['content']);
    }

    private function processAIResponse(string $aiResponse): array
    {
        if (!str_contains($aiResponse, '=>')) {
            throw new RuntimeException('AI response does not contain separator "=>"');
        }

        [$sqlQuery, $dataType] = array_pad(explode('=>', $aiResponse), 2, 'unknown');
        $sqlQuery = trim($sqlQuery);
        $dataType = trim($dataType);
        Log::info('XGenerated SQL query:', ['sql' => $sqlQuery, 'datatype' => $dataType]);


        if (!$this->validateSqlQuery($sqlQuery)) {
            throw new RuntimeException('Generated SQL query is not valid');
        }


        return [$sqlQuery, $dataType];
    }

    private function validateSqlQuery(string $sqlQuery): bool
    {
        try {
            $normalizedQuery = trim(strtolower($sqlQuery));

            if (!$this->validateSqlSyntax($normalizedQuery)) {
                return false;
            }
            /*if (!$this->validateQueryLimit($normalizedQuery)) {
                return false;
            }

            if (!$this->validateAllowedTables($normalizedQuery)) {
                return false;
            }*/
            return true;

        } catch (\Exception $e) {
            Log::warning('SQL validation failed', ['error' => $e->getMessage()]);
            return false;
        }
    }

    private function validateSqlSyntax(string $normalizedQuery): bool
{
    // Log pour indiquer le début de la validation
    Log::info('Début de la validation de la syntaxe SQL', ['query' => $normalizedQuery]);

    // Liste des mots-clés interdits
    $forbiddenKeywords = [
        'drop', 'delete', 'truncate', 'alter', 'insert', 'grant',
        'revoke', 'exec', 'execute', 'merge', 'call', 'xp_', 'sp_',
        'sysobjects', 'syscolumns', 'information_schema', 'into outfile',
        'load_file', 'benchmark', 'sleep', 'delay', 'waitfor', 'pg_sleep',
        'dblink', 'sysdate', 'version'
    ];

    // Vérification des mots-clés interdits
    foreach ($forbiddenKeywords as $keyword) {
        if (str_contains($normalizedQuery, $keyword)) {
            Log::warning('Requête SQL rejetée : mot-clé interdit détecté', [
                'keyword' => $keyword,
                'query' => $normalizedQuery,
            ]);
            return false;
        }
    }

    // Vérification que la requête commence par SELECT ou WITH
    if (!preg_match('/^(select|with)\s+/i', $normalizedQuery)) {
        Log::warning('Requête SQL rejetée : ne commence pas par SELECT ou WITH', [
            'query' => $normalizedQuery,
        ]);
        return false;
    }

    // Vérification de l'absence de commentaires
    if (preg_match('/(\/\*|\*\/|--)/i', $normalizedQuery)) {
        Log::warning('Requête SQL rejetée : commentaires détectés', [
            'query' => $normalizedQuery,
        ]);
        return false;
    }

    // Vérification de l'absence de UNION
    if (preg_match('/\bunion\b/i', $normalizedQuery)) {
        Log::warning('Requête SQL rejetée : UNION détecté', [
            'query' => $normalizedQuery,
        ]);
        return false;
    }

    // Vérification qu'il n'y a qu'une seule instruction SQL
    if (substr_count($normalizedQuery, ';') > 1) {
        Log::warning('Requête SQL rejetée : plusieurs instructions détectées', [
            'query' => $normalizedQuery,
        ]);
        return false;
    }

    // Log pour indiquer que la requête est valide
    Log::info('Requête SQL validée avec succès', ['query' => $normalizedQuery]);
    return true;
}
    private function validateQueryLimit(string $normalizedQuery): bool
    {
        if (!preg_match('/\blimit\s+\d+\b/i', $normalizedQuery)) {
            $normalizedQuery .= ' LIMIT ' . self::MAX_RESULTS;
            return true;
        }

        preg_match('/\blimit\s+(\d+)\b/i', $normalizedQuery, $matches);
        return isset($matches[1]) && intval($matches[1]) <= self::MAX_RESULTS;
    }

    private function validateAllowedTables(string $normalizedQuery): bool
    {
        $allowedTables = $this->getAllowedTables();
        foreach ($allowedTables as $table) {
            if (str_contains($normalizedQuery, $table)) {
                return true;
            }
        }
        return false;
    }

    private function getAllowedTables(): array
    {
        return Cache::remember('allowed_tables', self::CACHE_DURATION, function () {
            $dbName = config('database.connections.mysql.database');
            if (empty($dbName)) {
                throw new RuntimeException('Database name not configured');
            }

            return array_map(function($table) use ($dbName) {
                $column = 'Tables_in_' . $dbName;
                return $table->$column;
            }, DB::select('SHOW TABLES'));
        });
    }
}
