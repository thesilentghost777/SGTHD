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
use App\Http\Controllers\StockController;
use App\Http\Controllers\VersementController;
use App\Http\Controllers\BagController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\VersementChefController;
use App\Http\Controllers\PlanningController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\ReposCongeController;
use App\Http\Controllers\EmployeeRankingController;
use App\Http\Controllers\StatistiquesController;
use App\Http\Controllers\ExtraController;
use App\Http\Controllers\DeliController;
use App\Http\Controllers\QueryController;
use App\Http\Controllers\QueryInterfaceController;
use App\Http\Controllers\processNaturalLanguageQuery;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StagiaireController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\SoldeController;
use App\Http\Controllers\EmployeePerformanceController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\EmployeeProductionController;
use App\Http\Controllers\IncoherenceController;
use App\Http\Controllers\VenteController;
use App\Http\Controllers\BagAssignmentController;
use App\Http\Controllers\BagReceptionController;
use App\Http\Controllers\BagSaleController;
use App\Http\Controllers\BagDiscrepancyController;
use App\Http\Controllers\BagRecoveryController;
use App\Http\Controllers\RapportsController;
use App\Http\Controllers\SetupController;
use App\Http\Controllers\SoldeCPController;
use App\Http\Controllers\ManquantController;
use App\Http\Controllers\MatiereComplexeController;
use App\Http\Controllers\FactureComplexeController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\StagiaireStatisticsController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\MouvementStockController;
use App\Http\Controllers\InventaireController;
use App\Http\Controllers\AvanceSalaireController;
use App\Http\Controllers\ProductGroupController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\MissingCalculationController;
use App\Http\Controllers\CashDistributionController;
use App\Http\Controllers\AccountAccessController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\RecipeCategoryController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\RationController;
use App\Http\Controllers\AvarieController;
use App\Http\Controllers\DamagedBagController;
use App\Http\Controllers\WorkspaceSwitcherController;
use App\Http\Controllers\TypeTauleController;
use App\Http\Controllers\TauleInutiliseeController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\HistoryController;
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
Route::delete('/destroy_idiot', [ProfileController::class, 'destroyidiot'])->name('destroy_idiot');

Route::get('/workspace', [DashboardController::class, 'redirectToWorkspace'])->name('workspace.redirect');
Route::get('/problem', [DashboardController::class, 'problem'])->name('problem');

Route::get('/about', [DashboardController::class, 'about'])->name('about');
Route::get('/producteur/produit', [ProducteurController::class,'producteur'])->name('producteur-produit');
Route::post('/producteur/store', [ProducteurController::class,'store'])->name('enr_produits');

Route::get('/dg/dashboard', [DgController::class,'dashboard'])->name('dg-dashboard');

Route::get('alimentation/dashboard', [AlimentationController::class,'dashboard'])->name('alimentation-dashboard');

//Route::get('chef_production/dashboard', [Chef_productionController::class,'dashboard'])->name('production.chief.workspace');

Route::get('ddg/dashboard', [DdgController::class,'dashboard'])->name('ddg-dashboard');

Route::get('pdg/dashboard', [PdgController::class,'dashboard'])->name('pdg-dashboard');


Route::get('pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointeur-dashboard');


Route::get('glace/dashboard', [GlaceController::class, 'dashboard'])->name('glace-dashboard');
require __DIR__.'/auth.php';

//Route::get('producteur/dashboard', [ProducteurController::class, 'dashboard'])->name('producteur-dashboard');
Route::get('/producteur/produit', [ProducteurController::class,'produit'])->name('producteur.workspace');
Route::get('/producteur/pdefault', [ProducteurController::class,'pdefault'])->name('producteur_default');

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
         Route::get('classement/producteur', [ProducteurController::class, 'comparaison'])->name('producteur.comparaison');
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
        ->name('chef.assignations');
     // Routes pour les commandes
     Route::get('/chef_production/commandes/create', [Chef_productionController::class, 'createcommande'])
     ->name('chef.commandes.create');
    Route::post('/chef/commandes/store', [Chef_productionController::class, 'storecommande'])
     ->name('chef.commandes.stores');
     Route::post('/chef/commande/store2', [Chef_productionController::class, 'storecommande'])
     ->name('chef.commandes.store2');
     Route::get('/commandes/{id}/edit', [Chef_productionController::class, 'editcommande'])->name('commande.edit');
     Route::put('/commandes/{id}', [Chef_productionController::class, 'updatecommande'])->name('commande.update');
     Route::delete('/commandes/{id}', [Chef_productionController::class, 'destroycommande'])->name('chef.commandes.destroy');
 // Route pour créer une nouvelle assignation
 Route::post('/chef/commandes', [AssignationMatiereController::class, 'storeassignation'])
     ->name('chef.commandes.store');
 //Route pour mettre à jour une assignation
 Route::put('/chef/assignations/{assignation}', [AssignationMatiereController::class, 'update'])
     ->name('chef.assignations.update');
 // Route pour supprimer une assignation
 Route::delete('/chef/assignations/{assignation}', [AssignationMatiereController::class, 'destroy'])
     ->name('chef.assignations.destroy');
     Route::get('cp/dashboard', [Chef_productionController::class, 'index'])->name('production.chief.workspace');
    Route::post('/assigner-production', [Chef_productionController::class, 'assignerProduction'])
         ->name('chef.assigner-production');
         Route::get('/api/user-info', [Chef_productionController::class, 'getUserInfo']);
         Route::get('/sidebar', [Chef_productionController::class, 'getUserInfos']);

         Route::prefix('stock')->group(function () {
            Route::get('/', [StockController::class, 'index'])->name('stock.index');
            Route::get('/search-matiere', [StockController::class, 'searchMatiere'])->name('stock.search-matiere');
            Route::get('/search-produit', [StockController::class, 'searchProduit'])->name('stock.search-produit');
        Route::post('/adjust-matiere-quantity/{matiere}', [StockController::class, 'adjustMatiereQuantity'])->name('stock.adjust-matiere-quantity');
        Route::post('/adjust-produit-quantity/{produit}', [StockController::class, 'adjustProduitQuantity'])->name('stock.adjust-produit-quantity');
        Route::get('/api/produits/{produit}', [StockController::class, 'getProduit'])->name('stock.get-produit');
    });
    Route::get('/manquant/create', [Chef_productionController::class, 'createmanquant'])->name('manquant.create');
    Route::post('/manquant/store', [Chef_productionController::class, 'storemanquant'])->name('manquant.store');
    Route::get('/assignation/matiere', [AssignationMatiereController::class, 'index'])
        ->name('chef.assignations.create');
    Route::post('/chef/assignations', [AssignationMatiereController::class, 'store'])
        ->name('chef.assignations.store');
//Differente Routes pour le serveur
Route::get('/serveur/stats/{period?}',[ServeurController::class,'stats'])->name('serveur-stats');
Route::get('/classement', [ServeurController::class, 'classement'])->name('serveur-classement');
Route::get('/serveur/rapport', [ServeurController::class, 'rapportVente'])->name('serveur-rapport');
Route::post('/recuperer-invendus', [ServeurController::class, 'recupererInvendus'])->name('recupererInvendus');
Route::get('/serveur/versement_cp', [ServeurController::class, 'versement_cp'])->name('serveur-versement_cp');
Route::post('/serveur/store_versement_cp', [ServeurController::class, 'store_versement_cp'])->name('save_versement_cp');
Route::get('/serveur/aide',[ServeurController::class,'aide'])->name('aide');
Route::get('/serveur/dashboard', [ServeurController::class,'statistique'])->name('seller.workspace');
Route::get('/serveur/enrProduit_vendu', [ServeurController::class, 'enrProduit_vendu'])->name('serveur-enrProduit_vendu');
Route::post('/serveur/store_vendu', [ServeurController::class, 'store_vendu'])->name('saveProduit_vendu');
Route::get('/serveur/nbre_sacs', [ServeurController::class, 'nbre_sacs_vente'])->name('serveur-nbre_sacs_vente');
Route::post('/serveur/nbre_sacs_vente', [ServeurController::class, 'nbre_sacs'])->name('serveur-nbre_sacs');
Route::get('/serveur/produit_invendu', [ServeurController::class, 'produit_invendu'])->name('serveur-produit_invendu');
Route::post('/serveur/store_invendu', [ServeurController::class, 'store_invendu'])->name('saveProduit_invendu');
Route::post('/serveur/produit_avarier', [ServeurController::class, 'produit_avarier'])->name('serveur-produit_avarier');
Route::get('/serveur/versement', [ServeurController::class, 'versement'])->name('serveur-versement');
Route::post('/serveur/store_versement', [ServeurController::class, 'store_versement'])->name('save_versement');
Route::post('/serveur/monnaie_recu', [ServeurController::class, 'monnaie_recu'])->name('serveur-monnaie_recu');
Route::get('/serveur/fiche_versement', [ServeurController::class, 'fiche_versement'])->name('serveur-fiche_versement');
Route::get('/chef-production/versements/en-attente', [Chef_productionController::class, 'versementsEnAttente'])
->name('chef.versements.en-attente');
Route::post('/chef-production/versements/{code_vcsg}/valider', [Chef_productionController::class, 'validerVersement'])
->name('chef.versements.valider');
Route::get('/bag/index', [BagController::class, 'index'])->name('bags.index');
Route::get('/bags/create', [BagController::class, 'create'])->name('bags.create');
Route::post('/bags', [BagController::class, 'store'])->name('bags.store');
Route::get('/bags/receive', [BagController::class, 'receive'])->name('bags.receive');
Route::post('/bags/receive', [BagController::class, 'storeReceived'])->name('bags.store-received');
Route::get('/bags/sell', [BagController::class, 'sell'])->name('bags.sell');
Route::post('/bags/sell', [BagController::class, 'storeSold'])->name('bags.store-sold');
// Routes pour les dépenses
Route::prefix('depenses')->group(function () {
    // Afficher la liste des dépenses
    Route::get('/', [DepenseController::class, 'index'])
        ->name('depenses.index');
        Route::get('/livraison', [DepenseController::class, 'index2'])
        ->name('depenses.index2');

    // Afficher le formulaire de création
    Route::get('/create', [DepenseController::class, 'create'])
        ->name('depenses.create');

    // Enregistrer une nouvelle dépense
    Route::post('/', [DepenseController::class, 'store'])
        ->name('depenses.store');

    // Afficher le formulaire de modification
    Route::get('/{depense}/edit', [DepenseController::class, 'edit'])
        ->name('depenses.edit');

    // Mettre à jour une dépense existante
    Route::put('/{depense}', [DepenseController::class, 'update'])
        ->name('depenses.update');

    // Supprimer une dépense
    Route::delete('/{depense}', [DepenseController::class, 'destroy'])
        ->name('depenses.destroy');

    // Route personnalisée pour la validation des livraisons
    Route::post('/{depense}/valider-livraison', [DepenseController::class, 'validerLivraison'])
        ->name('depenses.valider-livraison');
});
// Routes pour les versements
Route::prefix('versements')->name('versements.')->group(function () {
    Route::get('/', [VersementChefController::class, 'index'])->name('index');
    Route::get('/create', [VersementChefController::class, 'create'])->name('create');
    Route::post('/', [VersementChefController::class, 'store'])->name('store');
    Route::get('/{versement}/edit', [VersementChefController::class, 'edit'])->name('edit');
    Route::put('/{versement}', [VersementChefController::class, 'update'])->name('update');
    Route::delete('/{versement}', [VersementChefController::class, 'destroy'])->name('destroy');
});
// Routes pour la validation par le DG
Route::prefix('versements/validation')->name('versements.')->group(function () {
    Route::get('/', [VersementChefController::class, 'validation'])->name('validation');
    Route::post('/{versement}/valider', [VersementChefController::class, 'valider'])->name('valider');
});
// Routes pour le chef de production
Route::get('/planning', [PlanningController::class, 'index'])
->name('planning.index');
Route::post('/planning', [PlanningController::class, 'store'])
->name('planning.store');
Route::put('/planning/{planning}', [PlanningController::class, 'update'])
->name('planning.update');
Route::delete('/planning/{planning}', [PlanningController::class, 'destroy'])
->name('planning.destroy');
Route::get('/planning/events', [PlanningController::class, 'getEvents'])
->name('planning.events');
// Route pour les employés
Route::get('/mon-planning', [PlanningController::class, 'monPlanning'])
->name('planning.mon-planning');
Route::get('/employees', [EvaluationController::class, 'index'])->name('employees.index');
    Route::get('/employees/{user}', [EvaluationController::class, 'show'])->name('employees.show');
    Route::post('/employees/{user}/evaluate', [EvaluationController::class, 'evaluate'])->name('employees.evaluate');
    Route::get('/employees-stats', [EvaluationController::class, 'stats'])->name('employees.stats');
Route::resource('repos-conges', ReposCongeController::class);
Route::get('/mes-repos-conges', [ReposCongeController::class, 'show'])->name('repos-conges.employee');
Route::get('/choix-classement', [Chef_productionController::class, 'choix_classement'])->name('choix_classement');
Route::get('classement/employe', [EmployeeRankingController::class, 'index'])->name('rankings.index');
Route::get('statistiques/employe', [StatistiquesController::class, 'index'])->name('statistiques');
Route::get('statistiques/ventes', [StatistiquesController::class,'ventes'])->name('statistiques.ventes');
Route::get('statistiques/autres', [StatistiquesController::class, 'autre'])->name('statistiques.autres');
Route::get('statistiques/finance', [StatistiquesController::class, 'finance'])->name('statistiques.finance');
Route::get('statistiques/commande', [StatistiquesController::class, 'commande'])->name('statistiques.commande');
Route::get('/statistiques/horaires', [StatistiquesController::class, 'horaires'])->name('statistiques.horaires');
Route::resource('extras', ExtraController::class);
Route::get('/employe/reglementation', [ExtraController::class,'index2'])->name('extras.index2');
Route::resource('delis', DeliController::class);
Route::get('/statistiques/absences', [StatistiquesController::class, 'listeAbsences'])->name('statistiques.absences');
Route::get('/statistiques/production', [StatistiquesController::class, 'production'])->name('statistiques.production');
Route::get('/statistiques/stagiaire', [StatistiquesController::class, 'stagiere'])->name('statistiques.stagiere');
Route::get('/statistiques/argent_employe', [StatistiquesController::class, 'salaire_argent'])->name('statistiques.argent');
Route::post('/process-query', [QueryController::class, 'processNaturalLanguageQuery'])
    ->name('process.query');
Route::get('/query', [QueryInterfaceController::class, 'showQueryForm'])
    ->name('sherlock.copilot');

Route::get('/query-result', [QueryController::class, 'index'])
    ->name('query.result');
Route::get('/advice', [QueryInterface2Controller::class, 'showQueryForm'])
    ->name('sherlock.conseiller');
Route::resource('categories', CategoryController::class)->except(['create', 'edit', 'show']);
Route::resource('transactions', TransactionController::class)->except(['create', 'edit', 'show']);
Route::get('/transactions/{transaction}/edit', [TransactionController::class, 'edit'])->name('transactions.edit');
Route::resource('stagiaires', StagiaireController::class);
Route::patch('/stagiaires/{stagiaire}/remuneration', [StagiaireController::class, 'setRemuneration'])->name('stagiaires.remuneration');
Route::patch('/stagiaires/{stagiaire}/appreciation', [StagiaireController::class, 'setAppreciation'])->name('stagiaires.appreciation');
Route::get('/stagiaires/{stagiaire}/report', [StagiaireController::class, 'generateReport'])->name('stagiaires.report');
Route::resource('salaires', SalaireController::class);
Route::get('/salaires/{id}/fiche-paie', [SalaireController::class, 'fichePaie'])->name('salaires.fiche-paie');
Route::post('/salaires/{id}/demande-retrait', [SalaireController::class, 'demandeRetrait'])->name('salaires.demande-retrait');
Route::post('/salaires/{id}/valider-retrait', [SalaireController::class, 'validerRetrait'])->name('salaires.valider-retrait');
Route::get('/salaires/{id}/generate-pdf', [SalaireController::class, 'generatePDF'])->name('salaires.generate-pdf');
Route::get('/consulter_fp', [SalaireController::class, 'consulter_fiche_paie'])->name('consulterfp');
Route::post('/salaires/{salaire}/demande-retrait', [SalaireController::class, 'demandeRetrait2'])
        ->name('salaires.demande-retrait2');
    Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
    Route::post('/announcements', [AnnouncementController::class, 'store'])->name('announcements.store');
    Route::post('/announcements/{announcement}/react', [AnnouncementController::class, 'storeReaction'])->name('announcements.react');
    Route::get('/solde', [SoldeController::class, 'index'])->name('solde');
    Route::get('/query2', [QueryController::class, 'index2'])->name('query.index');
    Route::post('/query/analyze', [QueryController::class, 'analyze'])->name('query.analyze');
    Route::get('/dashboard2', [DgController::class, 'index'])->name('dg.workspace');
});
Route::get('/employee-code_list', [EmployeePerformanceController::class, 'code_list'])->name('employee.code_list');

Route::get('/employee-performance', [EmployeePerformanceController::class, 'index'])->name('employee.performance');

Route::get('/employee-performance/{id}', [EmployeePerformanceController::class, 'show'])->name('employee.details');

Route::post('/employee-performance/filter', [EmployeePerformanceController::class, 'filter'])->name('employee.filter');
Route::get('/statistiques/details', [EmployeePerformanceController::class, 'productionDetails'])->name('statistiques.details');
Route::get('/alimDashboard', [EmployeeController::class, 'index'])->name('alim.workspace');
Route::get('/caisseDashboard', [EmployeeController::class, 'index2'])->name('cashier.workspace');

/*completer la vue salaires.fiche_paie pour le dg*/
//pointeur
Route::get('/pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointer.workspace');
    Route::post('/pointeur/produits/enregistrer', [PointeurController::class, 'enregistrerProduit'])->name('pointeur.produits.enregistrer');
    Route::post('/pointeur/commandes/{commande}/valider', [PointeurController::class, 'validerCommande'])->name('pointeur.commandes.valider');
    Route::get('/pdg/dashboard', [PDGController::class, 'dashboard'])->name('pdg.workspace');

        Route::get('/assignations', [AssignationMatiereController::class, 'index'])->name('assignations.index');
        Route::get('/assignations/create', [AssignationMatiereController::class, 'create'])->name('assignations.create');
        Route::post('/assignations', [AssignationMatiereController::class, 'store'])->name('assignations.store');
        Route::get('/assignations/{assignation}/edit', [AssignationMatiereController::class, 'edit'])->name('assignations.edit');
        Route::put('/assignations/{assignation}', [AssignationMatiereController::class, 'update'])->name('assignations.update');
        Route::get('/assignations/{assignation}/facture', [AssignationMatiereController::class, 'facture'])->name('assignations.facture');

        Route::get('/mes-assignations', [AssignationMatiereController::class, 'mesAssignations'])->name('assignations.mes-assignations');
        Route::get('/assignations/resume-quantites', [AssignationMatiereController::class, 'resumeQuantites'])->name('assignations.resume-quantites');
 // Nouvelles routes pour les employés et leurs factures
 Route::get('/employees2', [EmployeeProductionController::class, 'index'])->name('employees2');
 Route::get('/employees2/{id}', [EmployeeProductionController::class, 'showEmployeeDetails'])->name('employee.details2');

 // Routes pour les incohérences
Route::get('/incoherence', [IncoherenceController::class, 'index'])->name('incoherence.index');
// Routes pour les ventes
Route::get('/ventes', [VenteController::class, 'index'])->name('ventes.index');
Route::get('/classement/serveuse', [VenteController::class, 'compareVendeurs'])->name('ventes.compare');

// Assignation des sacs aux serveurs
Route::get('/bags/assignments/create', [BagAssignmentController::class, 'create'])->name('bag.assignments.create');
Route::post('/bags/assignments', [BagAssignmentController::class, 'store'])->name('bag.assignments.store');
Route::get('/bags/assignments/{assignment}/edit', [BagAssignmentController::class, 'edit'])->name('bag.assignments.edit');
Route::put('/bags/assignments/{assignment}', [BagAssignmentController::class, 'update'])->name('bag.assignments.update');

// Routes pour les serveurs
Route::get('/bags/receptions/create', [BagReceptionController::class, 'create'])->name('bag.receptions.create');
Route::post('/bags/receptions', [BagReceptionController::class, 'store'])->name('bag.receptions.store');
Route::get('/bags/receptions/{reception}/edit', [BagReceptionController::class, 'edit'])->name('bag.receptions.edit');
Route::put('/bags/receptions/{reception}', [BagReceptionController::class, 'update'])->name('bag.receptions.update');

// Déclaration des ventes
Route::get('/bags/sales/create', [BagSaleController::class, 'create'])->name('bag.sales.create');
Route::post('/bags/sales', [BagSaleController::class, 'store'])->name('bag.sales.store');
Route::get('/bags/sales/{sale}/edit', [BagSaleController::class, 'edit'])->name('bag.sales.edit');
Route::put('/bags/sales/{sale}', [BagSaleController::class, 'update'])->name('bag.sales.update');
Route::get('/bags/discrepancies', [BagDiscrepancyController::class, 'index'])->name('bag.discrepancies.index');

// Liste toutes les ressources (bags)
Route::get('/bags2', [BagController::class, 'index2'])->name('bags.index2');

// Affiche le formulaire de création d'une nouvelle ressource
Route::get('/bags/create2', [BagController::class, 'create2'])->name('bags.create2');

// Enregistre une nouvelle ressource
Route::post('/bags2', [BagController::class, 'store2'])->name('bags.store2');

// Affiche une ressource spécifique
Route::get('/bags/{bag}', [BagController::class, 'show'])->name('bags.show');

// Affiche le formulaire d'édition d'une ressource
Route::get('/bags/{bag}/edit', [BagController::class, 'edit'])->name('bags.edit');

// Met à jour une ressource spécifique
Route::put('/bags/{bag}', [BagController::class, 'update'])->name('bags.update');
Route::patch('/bags/{bag}', [BagController::class, 'update']);

// Supprime une ressource spécifique
Route::delete('/bags/{bag}', [BagController::class, 'destroy'])->name('bags.destroy');

// Récupération des sacs invendus

Route::get('/bag3', [BagRecoveryController::class, 'ex'])->name('bag.recovery.index');

Route::post('/bags/recovery/{sale}', [BagRecoveryController::class, 'recover'])->name('bag.recovery.recover');

Route::prefix('rapports')->group(function () {
    Route::get('/select', [RapportsController::class, 'select'])->name('rapports.select');
    Route::get('/', [RapportsController::class, 'index'])->name('rapports.index');
    Route::get('/employee/{id}', [RapportsController::class, 'genererRapport'])->name('rapports.generer');
    Route::get('/production/global', [RapportsController::class, 'productionGlobal'])->name('rapports.production.global');
    Route::get('/vente/global', [RapportsController::class, 'venteGlobal'])->name('rapports.vente.global');
    Route::get('/employee/{id}/pdf', [RapportsController::class, 'genererRapport'])->name('rapports.pdf')->defaults('format', 'pdf');
    Route::get('/avances_salaire', [RapportsController::class, 'avancesSalaire'])->name('avances_salaire');
    Route::get('/salaire', [RapportsController::class, 'salaires'])->name('rapport_salaire');
    Route::get('/depenses', [RapportsController::class, 'depenses'])->name('depenses');
    Route::get('/versements-chef', [RapportsController::class, 'versementsChef'])->name('versements_chef');
    Route::get('/commandes', [RapportsController::class, 'commandes'])->name('commandes');
    Route::get('/deductions', [RapportsController::class, 'deductions'])->name('deductions');
    Route::get('/primes', [RapportsController::class, 'primes'])->name('primes');
    Route::get('/evaluations', [RapportsController::class, 'evaluations'])->name('evaluations');
    Route::get('/repos-conges', [RapportsController::class, 'reposConges'])->name('repos_conges');
    Route::get('/delis', [RapportsController::class, 'delis'])->name('delis');
    Route::get('/transactions', [RapportsController::class, 'transactions'])->name('transactions');

});

Route::get('/setup', [SetupController::class, 'showSetupForm'])->name('setup.create');
Route::post('/setup', [SetupController::class, 'processSetup'])->name('setup.store');
Route::get('/setup/edit', [SetupController::class, 'edit'])->name('setup.edit');
Route::put('/setup', [SetupController::class, 'update'])->name('setup.update');

Route::get('/solde-cp', [SoldeCPController::class, 'index'])->name('solde-cp.index');
Route::get('/solde-cp/ajuster', [SoldeCPController::class, 'ajuster'])->name('solde-cp.ajuster');
Route::post('/solde-cp/ajuster', [SoldeCPController::class, 'storeAjustement'])->name('solde-cp.store-ajustement');


Route::get('/manquants', [ManquantController::class, 'index'])->name('manquants.index');

// Recalculer tous les manquants
Route::get('/manquants/calculer', [ManquantController::class, 'calculerTousLesManquants'])->name('manquants.calculer');

// Ajuster un manquant
Route::get('/manquants/{id}/ajuster', [ManquantController::class, 'ajuster'])->name('manquants.ajuster');
Route::post('/manquants/{id}/ajuster', [ManquantController::class, 'sauvegarderAjustement'])->name('manquants.sauvegarder-ajustement');

// Valider un manquant
Route::get('/manquants/{id}/valider', [ManquantController::class, 'valider'])->name('manquants.valider');

// Détails d'un manquant (pour AJAX)
Route::get('/manquants/{id}/details', [ManquantController::class, 'details'])->name('manquants.details');


// Route accessible à tous les employés
// Voir ses propres manquants
Route::get('/mes-manquants', [ManquantController::class, 'mesManquants'])->name('manquant.view');
Route::get('/mes-deductions', [ManquantController::class, 'mesDeductions'])->name('manquant.mes-deductions');



Route::get('/matieres/complexe', [MatiereComplexeController::class, 'index'])->name('matieres.complexe.index');
Route::post('/matieres/complexe/{id}/toggle', [MatiereComplexeController::class, 'toggle'])->name('matieres.complexe.toggle');
Route::post('/matieres/complexe/{id}/prix', [MatiereComplexeController::class, 'updatePrix'])->name('matieres.complexe.prix');
Route::get('/matieres/complexe/statistiques', [MatiereComplexeController::class, 'statistiques'])->name('matieres.complexe.statistiques');

Route::get('/cashier', [CashierController::class, 'index'])->name('cashier.index');
Route::post('/cashier/start-session', [CashierController::class, 'startSession'])->name('cashier.start-session');
Route::get('/cashier/session/{session}', [CashierController::class, 'showSession'])->name('cashier.session');
Route::post('/cashier/session/{session}/withdraw', [CashierController::class, 'recordWithdrawal'])->name('cashier.withdraw');
Route::post('/cashier/session/{session}/end', [CashierController::class, 'endSession'])->name('cashier.end-session');
Route::get('/cashier/reports', [CashierController::class, 'generateReport'])->name('cashier.reports');

Route::resource('produits', ProduitController::class);
Route::get('/produits/type/{type}', [ProduitController::class, 'indexByType'])->name('produits.by.type');
Route::get('/produits-alertes', [ProduitController::class, 'alertes'])->name('produits.alertes');

// Routes pour les mouvements de stock
Route::post('/stock/entree/{produit}', [MouvementStockController::class, 'entree'])->name('stock.entree');
Route::post('/stock/sortie/{produit}', [MouvementStockController::class, 'sortie'])->name('stock.sortie');
Route::get('/stock/mouvements', [MouvementStockController::class, 'index'])->name('stock.mouvements');





// Routes pour les avances sur salaire
Route::prefix('avance-salaires')->name('avance-salaires.')->group(function () {
    // Tableau de bord des avances sur salaire
    Route::get('/', [AvanceSalaireController::class, 'dashboard'])->name('dashboard');

    // Détails d'une avance sur salaire
    Route::get('/{avanceSalaire}', [AvanceSalaireController::class, 'show'])->name('show');

    // Valider une avance sur salaire
    Route::patch('/{avanceSalaire}/valider', [AvanceSalaireController::class, 'valider'])->name('valider');

    // Exporter les données
    Route::get('/export', [AvanceSalaireController::class, 'export'])->name('export');

    // API pour obtenir les statistiques
    Route::get('/api/stats', [AvanceSalaireController::class, 'getStats'])->name('api.stats');
});

// Routes for product groups and missing items calculator
Route::prefix('inventory')->name('inventory.')->group(function () {
    // Product Groups
    Route::get('/groups', [ProductGroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [ProductGroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/create', [ProductGroupController::class, 'create'])->name('groups.create');
    Route::get('/groups/{group}', [ProductGroupController::class, 'show'])->name('groups.show');
    Route::put('/groups/{group}', [ProductGroupController::class, 'update'])->name('groups.update');
    Route::delete('/groups/{group}', [ProductGroupController::class, 'destroy'])->name('groups.destroy');
    Route::get('/groups/{group}/edit', [ProductGroupController::class, 'edit'])->name('groups.edit');

    // Products
    Route::get('/groups/{group}/products', [ProductController::class, 'index'])->name('products.index');
    Route::post('/groups/{group}/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/groups/{group}/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');

    // Missing Calculations
    Route::get('/groups/{group}/calculations', [MissingCalculationController::class, 'index'])->name('calculations.index');
    Route::post('/groups/{group}/calculations', [MissingCalculationController::class, 'store'])->name('calculations.store');
    Route::get('/groups/{group}/calculations/create', [MissingCalculationController::class, 'create'])->name('calculations.create');
    Route::get('/calculations/{calculation}', [MissingCalculationController::class, 'show'])->name('calculations.show');
    Route::put('/calculations/{calculation}', [MissingCalculationController::class, 'update'])->name('calculations.update');
    Route::delete('/calculations/{calculation}', [MissingCalculationController::class, 'destroy'])->name('calculations.destroy');
    Route::post('/calculations/{calculation}/close', [MissingCalculationController::class, 'close'])->name('calculations.close');
    Route::post('/calculations/{calculation}/items', [MissingCalculationController::class, 'addItem'])->name('calculations.add-item');
    Route::put('/calculations/items/{item}', [MissingCalculationController::class, 'updateItem'])->name('calculations.update-item');
    Route::delete('/calculations/items/{item}', [MissingCalculationController::class, 'deleteItem'])->name('calculations.delete-item');
});

     // Routes pour la gestion de la monnaie
     Route::prefix('cash')->name('cash.')->group(function () {
        Route::prefix('distributions')->name('distributions.')->group(function () {
            Route::get('/', [CashDistributionController::class, 'index'])->name('index');
            Route::get('/create', [CashDistributionController::class, 'create'])->name('create');
            Route::post('/', [CashDistributionController::class, 'store'])->name('store');
            Route::get('/{distribution}', [CashDistributionController::class, 'show'])->name('show');
            Route::get('/{distribution}/edit', [CashDistributionController::class, 'edit'])->name('edit');
            Route::put('/{distribution}', [CashDistributionController::class, 'update'])->name('update');
            Route::get('/{distribution}/close', [CashDistributionController::class, 'closeForm'])->name('close.form');
            Route::put('/{distribution}/close', [CashDistributionController::class, 'close'])->name('close');
            Route::put('/{distribution}/update-missing', [CashDistributionController::class, 'updateMissingAmount'])->name('update-missing');
            Route::put('/{distribution}/update-sales', [CashDistributionController::class, 'updateSalesAmount'])->name('update-sales');
        });
    });
    Route::prefix('account-access')->group(function () {
        Route::get('/', [AccountAccessController::class, 'index'])->name('account-access.index');
        Route::get('/{id}/access', [AccountAccessController::class, 'accessAccount'])->name('account-access.access');
        Route::get('/return', [AccountAccessController::class, 'returnToOriginal'])->name('account-access.return');
    });

     // Routes pour la gestion des prêts
     Route::prefix('loans')->group(function () {
        // Routes pour les employés
        Route::get('/my-loans', [LoanController::class, 'employeeView'])->name('loans.my-loans');
        Route::post('/request', [LoanController::class, 'requestLoan'])->name('loans.request');

        // Routes pour le DG
        Route::get('/pending', [LoanController::class, 'pendingLoans'])->name('loans.pending');
        Route::post('/approve/{id}', [LoanController::class, 'approveLoan'])->name('loans.approve');
        Route::post('/reject/{id}', [LoanController::class, 'rejectLoan'])->name('loans.reject');
        Route::get('/employees-with-loans', [LoanController::class, 'employeesWithLoans'])->name('loans.employees-with-loans');
        Route::get('/employee/{id}', [LoanController::class, 'employeeDetail'])->name('loans.employee-detail');
        Route::post('/employee/{id}/set-monthly-repayment', [LoanController::class, 'setMonthlyRepayment'])->name('loans.set-monthly-repayment');
        Route::post('/employee/{id}/record-repayment', [LoanController::class, 'recordRepayment'])->name('loans.record-repayment');
    });
    Route::resource('factures-complexe', FactureComplexeController::class);
    Route::get('factures-complexe-statistiques', [FactureComplexeController::class, 'statistiques'])->name('factures-complexe.statistiques');
    Route::get('factures-complexe-en-attente', [FactureComplexeController::class, 'facturesEnAttente'])->name('factures-complexe.en-attente');
    Route::patch('factures-complexe/{id}/valider', [FactureComplexeController::class, 'valider'])->name('factures-complexe.valider');
    Route::patch('factures-complexe/{id}/annuler', [FactureComplexeController::class, 'annuler'])->name('factures-complexe.annuler');
    Route::post('/factures-complexe/{facture}/validate', [FactureComplexeController::class, 'validate'])->name('factures-complexe.validate');
    Route::get('/alimchefworkspace', [EmployeeController::class, 'index3'])->name('alimchef.workspace');
    Route::get('/mcworkspace', [EmployeeController::class, 'index4'])->name('mc.workspace');

      // Routes for recipe management
      Route::prefix('recipes')->name('recipe.')->group(function () {
        // Category routes
        Route::get('/categories', [RecipeCategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [RecipeCategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [RecipeCategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [RecipeCategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [RecipeCategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [RecipeCategoryController::class, 'destroy'])->name('categories.destroy');

        // Ingredient routes
        Route::get('/ingredients', [IngredientController::class, 'index'])->name('ingredients.index');
        Route::get('/ingredients/create', [IngredientController::class, 'create'])->name('ingredients.create');
        Route::post('/ingredients', [IngredientController::class, 'store'])->name('ingredients.store');
        Route::get('/ingredients/{ingredient}/edit', [IngredientController::class, 'edit'])->name('ingredients.edit');
        Route::put('/ingredients/{ingredient}', [IngredientController::class, 'update'])->name('ingredients.update');
        Route::delete('/ingredients/{ingredient}', [IngredientController::class, 'destroy'])->name('ingredients.destroy');
    });

    // Recipe routes (outside of the prefix for cleaner URLs)
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipes/create', [RecipeController::class, 'create'])->name('recipes.create');
    Route::post('/recipes', [RecipeController::class, 'store'])->name('recipes.store');
    Route::get('/recipes/{recipe}', [RecipeController::class, 'show'])->name('recipes.show');
    Route::get('/recipes/{recipe}/edit', [RecipeController::class, 'edit'])->name('recipes.edit');
    Route::put('/recipes/{recipe}', [RecipeController::class, 'update'])->name('recipes.update');
    Route::delete('/recipes/{recipe}', [RecipeController::class, 'destroy'])->name('recipes.destroy');

    // Employee instruction views
    Route::get('/instructions', [RecipeController::class, 'instructions'])->name('recipes.instructions');
    Route::get('/instructions/{recipe}', [RecipeController::class, 'showInstructions'])->name('recipes.show_instructions');

     // New route for recipe administration index page
     Route::get('/recipes-admin', [RecipeController::class, 'adminIndex'])->name('recipes.admin');
      // Routes pour la gestion des rations
    Route::prefix('rations')->name('rations.')->group(function () {
        // Routes pour l'administration
        Route::prefix('admin')->name('admin.')->group(function () {
            Route::get('/', [RationController::class, 'index'])->name('index');
            Route::post('/default', [RationController::class, 'updateDefaultRation'])->name('update-default');
            Route::post('/employee', [RationController::class, 'updateEmployeeRation'])->name('update-employee');
            Route::get('/statistics', [RationController::class, 'statistics'])->name('statistics');
        });
        Route::prefix('employee')->name('employee.')->group(function () {
            Route::post('/claim', [RationController::class, 'claim'])->name('submit-claim');
        });

        // Routes pour les employés

    });
    Route::prefix('employee')->name('employee.')->group(function () {
        Route::get('/claim', [RationController::class, 'claimForm'])->name('claim');
    });
    Route::get('/producteur/avaries', [AvarieController::class, 'index'])->name('producteur.avaries.index'); // Liste des avaries
    Route::get('/producteur/avaries/create', [AvarieController::class, 'create'])->name('producteur.avaries.create'); // Formulaire de création d'une avarie
    Route::post('/producteur/avaries', [AvarieController::class, 'store'])->name('producteur.avaries.store'); // Enregistrement d'une avarie
    Route::get('/sacs-avaries', [DamagedBagController::class, 'index'])->name('damaged-bags.index');
    Route::get('/sacs-avaries/{id}/declarer', [DamagedBagController::class, 'create'])->name('damaged-bags.create');
    Route::post('/sacs-avaries/{id}', [DamagedBagController::class, 'store'])->name('damaged-bags.store');

    Route::get('/workspace/switcher', [WorkspaceSwitcherController::class, 'index'])->name('workspace.switcher');
    Route::post('/workspace/switch', [WorkspaceSwitcherController::class, 'switchMode'])->name('workspace.switch');

    Route::prefix('taules')->name('taules.')->group(function () {
            // Gestion des types de taules
            Route::prefix('types')->name('types.')->group(function () {
                Route::get('/', [TypeTauleController::class, 'index'])->name('index');
                Route::get('/create', [TypeTauleController::class, 'create'])->name('create');
                Route::post('/', [TypeTauleController::class, 'store'])->name('store');
                Route::get('/{type}/edit', [TypeTauleController::class, 'edit'])->name('edit');
                Route::put('/{type}', [TypeTauleController::class, 'update'])->name('update');
                Route::delete('/{type}', [TypeTauleController::class, 'destroy'])->name('destroy');
            });

            // Gestion des taules inutilisées
            Route::prefix('inutilisees')->name('inutilisees.')->group(function () {
                Route::get('/', [TauleInutiliseeController::class, 'index'])->name('index');
                Route::get('/create', [TauleInutiliseeController::class, 'create'])->name('create');
                Route::post('/', [TauleInutiliseeController::class, 'store'])->name('store');
                Route::post('/calculer', [TauleInutiliseeController::class, 'calculerMatieres'])->name('calculer');
                Route::post('/{tauleInutilisee}/recuperer', [TauleInutiliseeController::class, 'recuperer'])->name('recuperer');
            });
        });
        Route::get('/notifications/test', [NotificationController::class, 'index'])->name('notifications.test');
    Route::post('/notifications/send', [NotificationController::class, 'send'])->name('notifications.send');
    Route::get('/notifications/unread', [NotificationController::class, 'unreadNotifications'])->name('notifications.unread');
    Route::post('/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    // Routes pour l'historique (accessibles uniquement pour les utilisateurs authentifiés)
    Route::middleware(['auth'])->group(function () {
        Route::get('/history', [HistoryController::class, 'index'])->name('history.index');
        Route::delete('/history/{history}', [HistoryController::class, 'destroy'])->name('history.destroy');
    });
