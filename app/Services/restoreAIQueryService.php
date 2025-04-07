<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class AIQueryService {
    private $apiKey;
    private $apiEndpoint = 'https://api.openai.com/v1/chat/completions';
    private $conversationHistory = [];
    private $cacheKey = 'gpt_conversation_history';
    private $cacheDuration = 30; // durée en minutes

    public function __construct() {
        $this->apiKey = config('services.openai.api_key');
        if (empty($this->apiKey)) {
            Log::error('Clé API OpenAI manquante');
            throw new \Exception('La clé API OpenAI n\'est pas configurée');
        }
        $this->loadOrInitializeConversation();
    }

    private function loadOrInitializeConversation() {
        $cachedHistory = Cache::get($this->cacheKey);

        if ($cachedHistory) {
            $this->conversationHistory = $cachedHistory;
            Log::info('Historique de conversation chargé depuis le cache');
        } else {
            $this->initializeConversation();
            Cache::put($this->cacheKey, $this->conversationHistory, $this->cacheDuration * 60);
            Log::info('Nouvelle conversation initialisée et mise en cache');
        }
    }

    private function initializeConversation() {
       $this->conversationHistory = [[
            'role' => 'system',
            'content' => "Tu es un assistant spécialisé en bases de données SQL. " .
                        "Schéma de la base de données : {$this->getDatabaseSchema2()}\n" .
                        "Règles pour les requêtes SQL:\n" .
                        "- Uniquement des requêtes commençant par SELECT\n" .
                        "- Recherche avec LIKE et wildcards (%)\n" .
                        "- Joins avec alias explicites\n" .
                        "- Limite de 100 résultats\n" .
                        "- Pas de requêtes de modification\n" .
                        "- Ne jamais renvoyer les champs created_at, updated_at, email_verified_at, password et remember_token sauf pour timeseries ou demande explicite\n" .
                        "- Toujours privilégier les données lisibles par l'humain (noms plutôt que IDs)\n" .
                        "- Faire des JOINs pour obtenir les informations descriptives plutôt que les IDs\n" .
                        "- Dans le cas ou tu veux retourner une information qui ne sera pas recuperer dans la BD tu peux toujours utiliser                       SELECT ton_information\n" .
                        "- Ta réponse doit TOUJOURS être en deux parties séparées par un | :\n" .
                        "  1. La requête SQL pure\n" .
                        "  2. Le type de données qui sera retourner (float, integer, date, string, boolean, emptyArray, timeSeries, simpleArray, objectArray, multiArray, object, geographical, hierarchical, network, matrix, pie, unknown) : ceci va " .
                        "aider dans la vue pour un afichage de donnees plus adaptatif en fonction des donnees retourner\n" .
                        "exemple de type pour te guider\n" .
                        " -SELECT * FROM users LIMIT 3;\n" .
                        " -\$dataType = simpleArray\n" .

                        " -SELECT date, value FROM sales_data ORDER BY date LIMIT 12;\n" .
                        " \$dataType = timeseries\n" .

                        " -SELECT category, COUNT(*) as count FROM products GROUP BY category;\n" .
                        "- \$dataType = pie\n" .

                        " - SELECT temperature FROM weather_data WHERE city = 'Paris' LIMIT 1;\n" .
                        "- \$dataType = float\n" .

                        " - SELECT name FROM users WHERE id = 1 LIMIT 1;\n" .
                        "- \$dataType = string\n" .

                        "- SELECT 'Bonjour a vous je suis votre assistant';\n" .
                        " \$dataType = string\n" .

                        " -SELECT city, lat, lng FROM stores;\n" .
                        " \$dataType = geographical;\n" .
                        "Exemple de réponse:\n" .
                            "SELECT e.nom, e.prenom FROM employees e WHERE e.id = 1 | string"

        ]];
    }

    private function getDatabaseSchema(): string {
        return Cache::remember('database_schema', 24 * 60, function () {
            $tables = DB::select('SHOW TABLES');
            $schemaDescription = "Schéma de la base de données:\n\n";
            $tableColumn = 'Tables_in_' . env('DB_DATABASE');

            foreach ($tables as $table) {
                $tableName = $table->$tableColumn;
                $columns = DB::select("DESCRIBE `{$tableName}`");
                $schemaDescription .= "Table '{$tableName}':\n";

                foreach ($columns as $column) {
                    $schemaDescription .= "- {$column->Field} ({$column->Type}): ";
                    $schemaDescription .= $column->Null === 'NO' ? 'Obligatoire' : 'Optionnel';
                    if ($column->Key === 'PRI') $schemaDescription .= ', Clé Primaire';
                    if ($column->Key === 'MUL') $schemaDescription .= ', Clé Étrangère';
                    $schemaDescription .= "\n";
                }
                $schemaDescription .= "\n";
            }
            return $schemaDescription;
        });
    }

    public function generateSqlQuery(string $userQuery): array {
        try {
            $connectionTest = @fsockopen("api.openai.com", 443);
            if (!$connectionTest) {
                throw new \Exception('Impossible de se connecter à OpenAI (port 443)');
            }
            fclose($connectionTest);

            if (empty(trim($userQuery))) {
                throw new \Exception('La requête utilisateur ne peut pas être vide');
            }

            $this->conversationHistory[] = [
                'role' => 'user',
                'content' => "Génère une requête SQL pour : '{$userQuery}'. N'oublie pas de séparer ta réponse en deux parties avec un | comme expliqué."
            ];

            // Mise à jour du cache avec le nouvel historique
            Cache::put($this->cacheKey, $this->conversationHistory, $this->cacheDuration * 60);

            $response = Http::timeout(30)->retry(3, 100)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->apiKey
                ])
                ->post($this->apiEndpoint, [
                    'model' => 'gpt-4o-mini',
                    'messages' => $this->conversationHistory,
                    'temperature' => 0.3,
                    'max_tokens' => 500
                ]);

            if ($response->failed()) {
                throw new \Exception('Erreur de l\'API OpenAI: ' . $response->status() . ' - ' . $response->body());
            }

            $aiResponse = trim($response['choices'][0]['message']['content'] ?? '');

            // Ajout de la réponse à l'historique
            $this->conversationHistory[] = [
                'role' => 'assistant',
                'content' => $aiResponse
            ];

            // Mise à jour du cache avec la réponse
            Cache::put($this->cacheKey, $this->conversationHistory, $this->cacheDuration * 60);

            list($sqlQuery, $dataType) = array_pad(explode('|', $aiResponse), 2, 'unknown');
            $sqlQuery = trim($sqlQuery);
            $dataType = trim($dataType);

            if (!$this->validateSqlQuery($sqlQuery)) {
                throw new \Exception('La requête SQL générée n\'est pas valide');
            }

            return [$sqlQuery, $dataType];

        } catch (\Exception $e) {
            Log::error('Exception lors de la génération de requête SQL', [
                'message' => $e->getMessage(),
                'user_query' => $userQuery,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function validateSqlQuery(string $sqlQuery): bool {
        try {
            $normalizedQuery = trim(strtolower($sqlQuery));
            $forbiddenKeywords = ['drop', 'delete', 'truncate', 'alter', 'insert', 'update', 'grant', 'revoke', 'exec', 'execute', 'merge', 'call', 'xp_', 'sp_', 'sysobjects', 'syscolumns', 'information_schema', 'into outfile', 'load_file', 'benchmark', 'sleep', 'delay', 'waitfor', 'pg_sleep', 'dblink', 'sysdate', 'version'];

            foreach ($forbiddenKeywords as $keyword) {
                if (str_contains($normalizedQuery, $keyword)) {
                    return false;
                }
            }

            if (!preg_match('/^(select|with)\s+/i', $normalizedQuery)) {
                return false;
            }

            if (preg_match('/(\/\*|\*\/|--)/i', $normalizedQuery)) {
                return false;
            }

            if (preg_match('/\bunion\b/i', $normalizedQuery)) {
                return false;
            }

            if (substr_count($normalizedQuery, ';') > 1) {
                return false;
            }

            if (!preg_match('/\blimit\s+\d+\b/i', $normalizedQuery)) {
                $sqlQuery .= ' LIMIT 100';
            } else {
                preg_match('/\blimit\s+(\d+)\b/i', $normalizedQuery, $matches);
                if (isset($matches[1]) && intval($matches[1]) > 100) {
                    return false;
                }
            }

            // Vérification des tables autorisées
            $allowedTables = $this->getAllowedTables();
            $hasAllowedTable = false;
            foreach ($allowedTables as $table) {
                if (str_contains($normalizedQuery, $table)) {
                    $hasAllowedTable = true;
                    break;
                }
            }

            if (!$hasAllowedTable) {
                return false;
            }

            try {
                DB::raw($sqlQuery);
            } catch (\Exception $e) {
                return false;
            }

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    private function getAllowedTables(): array {
        return Cache::remember('allowed_tables', 60 * 24, function () {
            $dbName = env('DB_DATABASE');
            return array_map(function($table) use ($dbName) {
                $column = 'Tables_in_' . $dbName;
                return $table->$column;
            }, DB::select('SHOW TABLES'));
        });
    }
}
