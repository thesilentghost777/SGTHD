<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmployeeApiController;


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
