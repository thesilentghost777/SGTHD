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

Route::get('/producteur/produit', [ProducteurController::class,'produit'])->name('producteur_produit');

Route::get('/producteur/pdefault', [ProducteurController::class,'pdefault'])->name('producteur_default');

Route::post('/producteur/store', [ProducteurController::class,'store'])->name('enr_produits');

Route::get('/dg/dashboard', [DgController::class,'dashboard'])->name('dg-dashboard');

Route::get('alimentation/dashboard', [AlimentationController::class,'dashboard'])->name('alimentation-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class,'dashboard'])->name('chef_production-dashboard');

Route::get('ddg/dashboard', [DdgController::class,'dashboard'])->name('ddg-dashboard');

Route::get('pdg/dashboard', [PdgController::class,'dashboard'])->name('pdg-dashboard');

Route::get('serveur/dashboard', [ServeurController::class,'dashboard'])->name('serveur-dashboard');


Route::get('pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointeur-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class, 'dashboard'])->name('chef_production-dashboard');

Route::get('glace/dashboard', [GlaceController::class, 'dashboard'])->name('glace-dashboard');

Route::get('producteur/reserverMp', [ProducteurController::class, 'reserverMp'])->name('producteur-reserverMp');


require __DIR__.'/auth.php';

Route::get('chef_production/gestion_employe', [Chef_productionController::class, 'gestion_employe'])->name('chef_production-gestion_employe');

Route::get('chef_production/depense', [Chef_productionController::class, 'depense'])->name('chef_production-depense');

Route::get('pdg/depense', [PdgController::class, 'depense'])->name('pdg-depense');

Route::get('dg/rapports', [DgController::class, 'rapports'])->name('dg-rapports');

Route::get('serveur/versement', [ServeurController::class, 'versement'])->name('serveur-versement');

Route::get('producteur/fiche_production', [ProducteurController::class, 'fiche_production'])->name('producteur-fiche_production');

Route::get('producteur/commande', [ProducteurController::class, 'commande'])->name('producteur-commande');
Route::get('serveur/ajouterProduit_recu', [ServeurController::class, 'ajouterProduit_recu'])->name('serveur-ajouterProduit_recu');
Route::post('serveur/store', [ServeurController::class, 'store'])->name('addProduit_recu');
