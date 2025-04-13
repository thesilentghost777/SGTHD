<?php

namespace App\Http\Controllers;

use App\Services\AIQueryService;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

class QueryController extends Controller

{

    protected $aiQueryService;


    private const DATA_TYPE_TITLES = [

        'timeSeries' => 'Évolution temporelle',

        'pie' => 'Répartition',

        'table' => 'Tableau de données',

        'single' => 'Résultat',

        'boolean' => 'Résultat booléen',

        'array' => 'Données structurées'

    ];

    private const KEYWORDS_MAPPING = [

        'total' => 'Total',

        'moyenne' => 'Moyenne',

        'somme' => 'Somme',

        'nombre' => 'Nombre',

        'liste' => 'Liste',

        'évolution' => 'Évolution'

    ];


    public function __construct(AIQueryService $aiQueryService)

    {

        $this->aiQueryService = $aiQueryService;

    }
    public function index() {
        $sqlQuery = "SELECT 'test'";
        $results = $data = [
            (object) ['test']
        ];

        $userQuery = "test";
        $dataType = "string";
        $title = "test";

        return view('query-results', [
            'sqlQuery' => $sqlQuery,
            'data' => $results,
            'userQuery' => $userQuery,
            'dataType' => $dataType,
            'title' => $title
        ]);

    }

    public function processNaturalLanguageQuery(Request $request)

    {

        try {

            $userQuery = $request->input('query');

            if (empty(trim($userQuery))) {

                return $this->errorResponse('La requête ne peut pas être vide');

            }

            Log::info('Nouvelle requête utilisateur reçue', ['query' => $userQuery]);

            list($sqlQuery, $dataType) = $this->aiQueryService->generateSqlQuery($userQuery);

            if (!$sqlQuery) {

                Log::warning('Échec de génération de requête SQL', ['user_query' => $userQuery]);

                return $this->errorResponse(

                    'Impossible de générer une requête valide. Veuillez reformuler votre question.',

                    $userQuery

                );

            }

            $results = DB::select($sqlQuery);
            logger($results);

            $title = $this->generateTitle($userQuery, $dataType);

            Log::info('Requête exécutée avec succès', [

                'user_query' => $userQuery,

                'sql_query' => $sqlQuery,

                'results_count' => count($results),

                'data_type' => $dataType

            ]);

            return view('query-results', [

                'sqlQuery' => $sqlQuery,

                'data' => $results,

                'userQuery' => $userQuery,

                'dataType' => $dataType,

                'title' => $title

            ]);

        } catch (\Exception $e) {

            Log::error('Erreur lors du traitement de la requête', [

                'user_query' => $userQuery ?? null,

                'error' => $e->getMessage(),

                'trace' => $e->getTraceAsString()

            ]);

            return $this->errorResponse($e->getMessage(), $userQuery ?? null);

        }

    }

    protected function generateTitle(string $userQuery, string $dataType): string

    {

        $title = '';



        foreach (self::KEYWORDS_MAPPING as $keyword => $prefix) {

            if (stripos($userQuery, $keyword) !== false) {

                $title = $prefix . ' ';

                break;

            }

        }

        return $title . (self::DATA_TYPE_TITLES[$dataType] ?? 'Résultats de la requête');

    }

    private function errorResponse(string $message, ?string $userQuery = null)

    {

        return view('query-results', [

            'error' => $message,

            'userQuery' => $userQuery

        ]);

    }


    /*Mode barion : recuperations des details de n'importe quel table */
    protected function getRelatedTables($tableName)
    {
        $relatedTables = [];
        $foreignKeys = [];

        try {
            // Récupérer toutes les clés étrangères de la table
            $foreignKeys = DB::select("
                SELECT
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM
                    INFORMATION_SCHEMA.KEY_COLUMN_USAGE
                WHERE
                    TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = ?
                    AND REFERENCED_TABLE_NAME IS NOT NULL",
                [$tableName]
            );

            foreach ($foreignKeys as $fk) {
                $relatedTables[] = [
                    'column' => $fk->COLUMN_NAME,
                    'table' => $fk->REFERENCED_TABLE_NAME,
                    'referenced_column' => $fk->REFERENCED_COLUMN_NAME
                ];
            }
        } catch (\Exception $e) {
            Log::error("Erreur lors de la récupération des tables liées", [
                'error' => $e->getMessage(),
                'table' => $tableName
            ]);
        }

        return $relatedTables;
    }

    public function index2(){
        $tables = DB::select('SHOW TABLES');
        $tableList = array_map(function($table) {
            return reset($table);
        }, json_decode(json_encode($tables), true));

        return view('query.index', ['tables' => $tableList]);
    }

    public function analyze(Request $request)
    {
        $tableName = $request->input('table');
        $relatedTables = $this->getRelatedTables($tableName);

        // Construction du message pour les logs
        $message = "Analyse de la table {$tableName}\n";
        $message .= "Tables liées trouvées : " . count($relatedTables) . "\n";

        foreach ($relatedTables as $related) {
            $message .= "- {$related['table']} via {$related['column']}\n";
        }

        // Construction de la requête
        $query = "SELECT * FROM {$tableName}";
        if (!empty($relatedTables)) {
            foreach ($relatedTables as $related) {
                $query .= " LEFT JOIN {$related['table']} ON {$tableName}.{$related['column']} = {$related['table']}.{$related['referenced_column']}";
            }
        }
        if (!$this->validateSqlQuery($query)) {
            throw new RuntimeException('Generated SQL query is not valid');
        }

        try {
            $results = DB::select($query);
            $message .= "\nRequête exécutée avec succès. " . count($results) . " résultats trouvés.";

            Log::info($message, [
                'table' => $tableName,
                'query' => $query,
                'results_count' => count($results)
            ]);

            return view('query.results', [
                'results' => $results,
                'tableName' => $tableName,
                'relatedTables' => $relatedTables,
                'message' => $message
            ]);

        } catch (\Exception $e) {
            Log::error("Erreur lors de l'exécution de la requête", [
                'error' => $e->getMessage(),
                'query' => $query
            ]);

            return back()->with('error', "Erreur lors de l'analyse : " . $e->getMessage());
        }
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

}
