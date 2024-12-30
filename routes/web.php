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

Route::get('producteur/dashboard', [ProducteurController::class, 'dashboard'])->name('producteur-dashboard');

Route::get('serveur/ajouterProduit_recu', [ServeurController::class, 'ajouterProduit_recu'])->name('serveur-ajouterProduit_recu');
Route::post('serveur/store', [ServeurController::class, 'store'])->name('addProduit_recu');

Route::get('serveur/dashboard', [ServeurController::class,'dashboard'])->name('serveur-dashboard');
Route::get('serveur/enrProduit_vendu', [ServeurController::class, 'enrProduit_vendu'])->name('serveur-enrProduit_vendu');
Route::post('serveur/store_vendu', [ServeurController::class, 'store_vendu'])->name('saveProduit_vendu');
Route::post('serveur/nbre_sacs_vendu', [ServeurController::class, 'nbre_sacs_vendu'])->name('serveur-nbre_sacs_vendu');

Route::post('serveur/produit_invendu', [ServeurController::class, 'produit_invendu'])->name('serveur-produit_invendu');

Route::post('serveur/produit_avarier', [ServeurController::class, 'produit_avarier'])->name('serveur-produit_avarier');

Route::post('serveur/versement', [ServeurController::class, 'versement'])->name('serveur-versement');

Route::post('serveur/monnaie_recu', [ServeurController::class, 'monnaie_recu'])->name('serveur-monnaie_recu');

Route::get('serveur/fiche_versement', [ServeurController::class, 'fiche_versement'])->name('serveur-fiche_versement');
