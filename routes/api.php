<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeApiController;
use App\Http\Controllers\Api\ServeurApiController;
use App\Http\Controllers\Api\VenteApiController;



// Supprimer le middleware 'guest' (incompatible avec Sanctum)
// Utiliser Sanctum pour gérer les sessions stateful
Route::post('/api/login', [AuthController::class, 'login'])->middleware('web');
Route::post('/api/register', [AuthController::class, 'register']);

// Protéger les routes avec Sanctum (stateless)
    Route::post('/api/logout', [AuthController::class, 'logout']);
    Route::get('/api/user', [AuthController::class, 'user']);
    Route::get('/api/manquants', [EmployeeApiController::class, 'showManquants']);

    // Primes
    Route::get('/api/primes', [EmployeeApiController::class, 'getPrimes']);

    // Horaires
    Route::get('/api/horaires', [EmployeeApiController::class, 'getHoraires']);
    Route::post('/api/horaires/arrivee', [EmployeeApiController::class, 'marquerArrivee']);
    Route::post('/api/horaires/depart', [EmployeeApiController::class, 'marquerDepart']);
    Route::post('/api/horaires/enregistrer', [EmployeeApiController::class, 'enregistrerHoraire']);

    // Repos et congés
    Route::get('/api/repos-conges', [EmployeeApiController::class, 'getReposConges']);

    // Fiche de paie
    Route::get('/api/fiche-paie', [EmployeeApiController::class, 'getFichePaie']);
    Route::post('/api/fiche-paie/retrait', [EmployeeApiController::class, 'demandeRetrait']);

    // Prêts
    Route::get('/api/prets', [EmployeeApiController::class, 'getLoanInfo']);
    Route::post('/api/prets/demande', [EmployeeApiController::class, 'requestLoan']);

    // Rations
    Route::get('/api/rations', [EmployeeApiController::class, 'getRationInfo']);
    Route::post('/api/rations/reclamer', [EmployeeApiController::class, 'claimRation']);

    // Avance sur salaire
    Route::get('/api/avance-salaire/status', [EmployeeApiController::class, 'getAvanceSalaireStatus']);
    Route::get('/api/avance-salaire/eligibility', [EmployeeApiController::class, 'checkAsEligibility']);
    Route::post('/api/avance-salaire/demande', [EmployeeApiController::class, 'requestAvanceSalaire']);
    Route::post('/api/avance-salaire/retrait', [EmployeeApiController::class, 'retraitAvanceSalaire']);

    // Messages
    Route::post('/api/messages/send', [EmployeeApiController::class, 'sendMessage']);

    // Annonces
    Route::get('/api/annonces', [EmployeeApiController::class, 'getAnnouncements']);
    Route::post('/api/annonces/reaction', [EmployeeApiController::class, 'reactToAnnouncement']);

    Route::post('/login_page', [AuthController::class, 'login']);

// Route publique pour register
Route::post('/register', [AuthController::class, 'register']);

// Route protégée, exemple : Dashboard
Route::middleware('auth:sanctum')->get('/dashboard', function () {
    return response()->json([
        'message' => 'Bienvenue dans le dashboard',
        'user' => auth()->user()
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('serveur')->group(function () {
        Route::get('/dashboard', [ServeurApiController::class, 'dashboard']);
        Route::get('/stats', [ServeurApiController::class, 'stats']);
        Route::get('/versements', [ServeurApiController::class, 'versements']);
        Route::post('/store-produit', [ServeurApiController::class, 'store']);
        Route::post('/store-vendu', [ServeurApiController::class, 'storeVendu']);
        Route::post('/store-invendu', [ServeurApiController::class, 'storeInvendu']);
        Route::post('/declare-avarie', [ServeurApiController::class, 'declareAvarie']);
        Route::post('/recuperer-invendus', [ServeurApiController::class, 'recupererInvendus']);
        Route::get('/produits-recus', [ServeurApiController::class, 'getProduitsRecus']);
        Route::post('/produits-recus', [ServeurApiController::class, 'storeProduitRecu']);

    });
    
    Route::prefix('vente')->group(function () {
        Route::get('/index', [VenteApiController::class, 'index']);
        Route::get('/compare-vendeurs', [VenteApiController::class, 'compareVendeurs']);
        Route::get('/produits', [VenteApiController::class, 'getProduits']);
    });
});
