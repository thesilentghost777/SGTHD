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

Route::get('serveur/dashboard', [ServeurController::class,'dashboard'])->name('serveur-dashboard');


Route::get('pointeur/dashboard', [PointeurController::class, 'dashboard'])->name('pointeur-dashboard');

Route::get('chef_production/dashboard', [Chef_productionController::class, 'dashboard'])->name('chef_production-dashboard');

Route::get('glace/dashboard', [GlaceController::class, 'dashboard'])->name('glace-dashboard');

Route::get('producteur/reserverMp', [ProducteurController::class, 'reserverMp'])->name('producteur-reserverMp');


require __DIR__.'/auth.php';
