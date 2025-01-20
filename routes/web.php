<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProducteurController;
use App\Http\Controllers\DgController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DdgController;
use App\Http\Controllers\PdgController;
use App\Http\Controllers\Chef_productionController;
use App\Http\Controllers\ServeurController;
use App\Http\Controllers\AlimentationController;
use App\Http\Controllers\GlaceController;
use App\Http\Controllers\PointeurController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\SalaireController;
use App\Http\Controllers\HoraireController;
use App\Http\Controllers\FichePaieController;
use App\Http\Controllers\PrimeController;
use App\Http\Controllers\RecetteController;
use App\Http\Controllers\ReservationMpController;
use App\Http\Controllers\AssignationMatiereController;
use App\Http\Controllers\EmployeStandardController;
use App\Http\Controllers\CaissiereController;
use App\Http\Middleware\CheckRole;
Route::get('/', function () {
    return view('index');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/producteur/produit', [ProducteurController::class,'producteur'])->name('producteur-produit');
Route::post('/producteur/store', [ProducteurController::class,'store'])->name('enr_produits');

Route::get('/dg/dashboard', [DgController::class,'dashboard'])->name('dg-dashboard');

Route::get('alimentation/dashboard', [AlimentationController::class,'dashboard'])->name('alimentation-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class,'dashboard'])->name('chef_production-dashboard');

Route::get('ddg/dashboard', [DdgController::class,'dashboard'])->name('ddg-dashboard');

Route::get('pdg/dashboard', [PdgController::class,'dashboard'])->name('pdg-dashboard');


Route::get('pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointeur-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class, 'dashboard'])->name('chef_production-dashboard');

Route::get('glace/dashboard', [GlaceController::class, 'dashboard'])->name('glace-dashboard');



require __DIR__.'/auth.php';

Route::get('producteur/dashboard', [ProducteurController::class, 'dashboard'])->name('producteur-dashboard');
Route::get('/producteur/produit', [ProducteurController::class,'produit'])->name('producteur_produit');
Route::get('/producteur/pdefault', [ProducteurController::class,'pdefault'])->name('producteur_default');

Route::get('producteur/dashboard', [ProducteurController::class, 'dashboard'])->name('producteur-dashboard');
Route::get('producteur/commande', [ProducteurController::class, 'commande'])->name('producteur-commande');
Route::get('serveur/ajouterProduit_recu', [ServeurController::class, 'ajouterProduit_recu'])->name('serveur-ajouterProduit_recu');
Route::post('serveur/store', [ServeurController::class, 'store'])->name('addProduit_recu');

Route::get('message', [MessageController::class, 'message'])->name('message');
Route::post('message/store_message', [MessageController::class, 'store_message'])->name('message-post');

Route::get('lecture_message', [MessageController::class, 'lecture_message'])->name('lecture_message');
Route::post('/messages/mark-read/{type}', [MessageController::class, 'markRead'])->name('messages.markRead');
Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');


Route::middleware(['auth'])->group(function () {
    // Routes pour les employés
    Route::get('/reclamer-as', [SalaireController::class, 'reclamerAs'])->name('reclamer-as');
    Route::post('/store-demandes-as', [SalaireController::class, 'store_demandes_AS'])->name('store-demandes-as');
    Route::get('/voir-status', [SalaireController::class, 'voir_Status'])->name('voir-status');
    Route::get('/validation-retrait', [SalaireController::class, 'validation_retrait'])->name('validation-retrait');
    Route::post('/recup-retrait', [SalaireController::class, 'recup_retrait'])->name('recup-retrait');

    // Routes pour le DG
        Route::get('/valider-as', [SalaireController::class, 'validerAs'])->name('valider-as');
        Route::post('/store-validation', [SalaireController::class, 'store_validation'])->name('store-validation');
        Route::get('/form-salaire', [SalaireController::class, 'form_salaire'])->name('form-salaire');
        Route::post('/store-salaire', [SalaireController::class, 'store_salaire'])->name('store-salaire');

    // Routes pour le CP
        Route::get('/valider-retraitcp', [SalaireController::class, 'valider_retraitcp'])->name('valider-retraitcp');
        Route::post('/recup-retrait-cp', [SalaireController::class, 'recup_retrait_cp'])->name('recup-retrait-cp');

        Route::get('/horaire', [HoraireController::class, 'index'])->name('horaire.index');
        Route::post('/horaire/arrivee', [HoraireController::class, 'marquerArrivee'])->name('horaire.arrivee');
        Route::post('/horaire/depart', [HoraireController::class, 'marquerDepart'])->name('horaire.depart');
        Route::post('/horaire/enregistrer', [HoraireController::class, 'enregistrerHoraire'])->name('horaire.enregistrer');

        Route::get('/fiche-paie', [FichePaieController::class, 'show'])->name('fiche-paie.show');

        Route::get('/producteur/stat-production', [ProducteurController::class, 'stat_prod'])
        ->name('producteur.sp');

        Route::get('/producteur/produit_mp', [ProducteurController::class, 'create'])
        ->name('produitmp');


        Route::get('/manquants', [MessageController::class, 'showManquants'])
        ->name('manquant');

        Route::get('/chef_production/ajouter_produits', [Chef_productionController::class, 'gestionProduits'])
        ->name('chef_produits');

        Route::get('/chef_production/ajouter_matiere', [Chef_productionController::class, 'gestionMatieres'])
        ->name('chef_matieres');

        Route::post('producteur/store2', [ProducteurController::class, 'store2'])
        ->name('utilisations.store');




        //cp
        // Gestion des produits
        Route::get('cp/produits', [Chef_productionController::class, 'gestionProduits'])
            ->name('chef.produits.index');
        Route::post('cp/produits', [Chef_productionController::class, 'storeProduit'])
            ->name('chef.produits.store');
        Route::put('cp/produits/{produit}', [Chef_productionController::class, 'updateProduit'])
            ->name('chef.produits.update');
        Route::delete('cp/produits/{produit}', [Chef_productionController::class, 'destroyProduit'])
            ->name('chef.produits.destroy');

        // Gestion des matières premières
        Route::get('cp/matieres', [Chef_productionController::class, 'gestionMatieres'])
            ->name('chef.matieres.index');
        Route::post('cp/matieres', [Chef_productionController::class, 'storeMatiere'])
            ->name('chef.matieres.store');
        Route::put('/chef/matieres/{matiere}', [Chef_productionController::class, 'updateMatiere'])->name('chef.matieres.update');
        Route::delete('/chef/matieres/{matiere}', [Chef_productionController::class, 'destroyMatiere'])->name('chef.matieres.destroy');
        Route::get('/chef/matieres/{matiere}/edit', [Chef_productionController::class, 'editMatiere'])->name('chef.matieres.edit');

        Route::get('/mes_primes', [PrimeController::class, 'index'])->name('primes.index');
        Route::get('/attribution-prime', [PrimeController::class, 'create'])
                 ->name('primes.create');
        Route::post('/attribution-prime', [PrimeController::class, 'store'])
                 ->name('primes.store');

         Route::get('/recettes', [RecetteController::class, 'index'])->name('recettes.index');
         Route::get('/recettes/create', [RecetteController::class, 'create'])->name('recettes.create');
         Route::post('/recettes', [RecetteController::class, 'store'])->name('recettes.store');
         Route::post('/recettes/calculate', [RecetteController::class, 'calculateIngredients'])->name('recettes.calculate');
         Route::delete('/recettes/{produit}', [RecetteController::class, 'destroy'])->name('recettes.destroy');
         Route::get('/producteur/comparaison', [ProducteurController::class, 'comparaison'])->name('producteur.comparaison');
         Route::get('/producteur/lots', [ProducteurController::class, 'produit_par_lot'])->name('producteur.lots');
         Route::get('/production/fiche', [ProducteurController::class, 'fiche_production'])->name('production.fiche');

         // Réservation de matières premières
    Route::get('/producteur/reserver-mp', [ReservationMpController::class, 'create'])
    ->name('producteur.reservations.create');
Route::post('/producteur/reserver-mp', [ReservationMpController::class, 'store'])
    ->name('producteur.reservations.store');

// Voir ses assignations
Route::get('/producteur/mes-assignations', [AssignationMatiereController::class, 'index'])
    ->name('producteur.assignations.index');

    // Gestion des réservations
    Route::get('/chef/reservations', [ReservationMpController::class, 'index'])
        ->name('chef.reservations.index');
    Route::post('/chef/reservations/{reservation}/valider', [ReservationMpController::class, 'validerReservation'])
        ->name('chef.reservations.valider');
    Route::post('/chef/reservations/{reservation}/refuser', [ReservationMpController::class, 'refuserReservation'])
        ->name('chef.reservations.refuser');

    // Assignation de matières premières
    Route::get('/chef/assignations/create', [AssignationMatiereController::class, 'create'])
        ->name('chef.assignations.create');
    Route::post('/chef/assignations', [AssignationMatiereController::class, 'store'])
        ->name('chef.assignations.store');
});
//Differente Routes pour le serveur
Route::get('serveur/stats/{period?}',[ServeurController::class,'stats'])->name('serveur-stats');

Route::get('/statistiques', [ServeurController::class, 'statistique'])->name('statistiques');

Route::get('/classement', [ServeurController::class, 'classement'])->name('serveur-classement');
Route::get('/serveur/rapport', [ServeurController::class, 'rapportVente'])->name('serveur-rapport');

Route::post('/recuperer-invendus', [ServeurController::class, 'recupererInvendus'])->name('recupererInvendus');

Route::get('serveur/versement_cp', [ServeurController::class, 'versement_cp'])->name('serveur-versement_cp');
Route::post('serveur/store_versement_cp', [ServeurController::class, 'store_versement_cp'])->name('save_versement_cp');
Route::get('serveur/aide',[ServeurController::class,'aide'])->name('aide');
Route::get('serveur/dashboard', [ServeurController::class,'dashboard'])->name('serveur-dashboard')->middleware('role:serveur');
Route::get('serveur/enrProduit_vendu', [ServeurController::class, 'enrProduit_vendu'])->name('serveur-enrProduit_vendu');
Route::post('serveur/store_vendu', [ServeurController::class, 'store_vendu'])->name('saveProduit_vendu');
Route::get('serveur/nbre_sacs', [ServeurController::class, 'nbre_sacs_vente'])->name('serveur-nbre_sacs_vente');
Route::post('serveur/nbre_sacs_vente', [ServeurController::class, 'nbre_sacs'])->name('serveur-nbre_sacs');

Route::get('serveur/produit_invendu', [ServeurController::class, 'produit_invendu'])->name('serveur-produit_invendu');
Route::post('serveur/store_invendu', [ServeurController::class, 'store_invendu'])->name('saveProduit_invendu');
Route::post('serveur/produit_avarier', [ServeurController::class, 'produit_avarier'])->name('serveur-produit_avarier');

Route::get('serveur/versement', [ServeurController::class, 'versement'])->name('serveur-versement');
Route::post('serveur/store_versement', [ServeurController::class, 'store_versement'])->name('save_versement');
Route::post('serveur/monnaie_recu', [ServeurController::class, 'monnaie_recu'])->name('serveur-monnaie_recu');

Route::get('serveur/fiche_versement', [ServeurController::class, 'fiche_versement'])->name('serveur-fiche_versement');
//pointeur
Route::get('pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointeur-dashboard');
Route::get('pointeur/ajouterProduit_recu', [PointeurController::class, 'ajouterProduit_recu'])->name('pointeur-ajouterProduit_recu');
Route::post('pointeur/store', [PointeurController::class,'store'])->name('pointeur-store');
Route::get('pointeur/afficheProduit', [PointeurController::class, 'afficheProduit'])->name('pointeur-afficheProduit_recu');
Route::get('pointeur/valider-commandes', [PointeurController::class, 'showNonValide'])->name('valider-commandes');
Route::post('pointeur/valider/{id}', [PointeurController::class, 'validerCommande'])->name('valider-commande');
Route::get('pointeur/classement', [PointeurController::class, 'comparaison'])->name('classement');
Route::get('produit/edit/{produit}', [PointeurController::class, 'edit'])->name('produit.edit');
Route::post('produit/update/{produit}', [PointeurController::class, 'update'])->name('produit.update');
Route::get('pointeur/statistique',[PointeurController::class,'statistique'])->name('statistique');
//employe lamda
Route::get('employe/dashboard', [EmployeStandardController::class, 'dashboard'])->name('employe-dashboard');
//Glace
Route::get('glace/dashboard', [GlaceController::class, 'dashboard'])->name('glace-dashboard');
Route::get('glace/vente', [GlaceController::class, 'vente'])->name('glace-vente');
Route::post('glace/store', [GlaceController::class, 'store'])->name('glace-store');
Route::get('glace/stats',[GlaceController::class,'stat'])->name('glace-stat');
//Caissier(e)
Route::get('caissier/dashboard',[CaissiereController::class,'dashboard'])->name('caisse-dashboard');
Route::get('caissier/stats',[CaissiereController::class,'stat'])->name('caisse-stat');

