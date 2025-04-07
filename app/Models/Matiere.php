<?php

namespace App\Models;

use App\Enums\UniteMinimale;
use App\Services\MatiereService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Matiere extends Model
{
    use HasFactory;
    protected $table = 'Matiere';
    protected $fillable = [
        'nom',
        'unite_minimale',
        'unite_classique',
        'quantite_par_unite',
        'quantite',
        'prix_unitaire',
        'prix_par_unite_minimale'
    ];

    protected $casts = [
        'unite_minimale' => UniteMinimale::class
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($matiere) {
            $service = new MatiereService();
            $matiere->prix_par_unite_minimale = $service->calculerPrixParUniteMinimale(
                $matiere->prix_unitaire,
                $matiere->quantite_par_unite,
                $matiere->unite_classique,
                $matiere->unite_minimale->value
            );
        });

        static::updating(function ($matiere) {
            $service = new MatiereService();
            $matiere->prix_par_unite_minimale = $service->calculerPrixParUniteMinimale(
                $matiere->prix_unitaire,
                $matiere->quantite_par_unite,
                $matiere->unite_classique,
                $matiere->unite_minimale->value
            );
        });
    }

    public function assignations(): HasMany
    {
        return $this->hasMany(AssignationMatiere::class, 'matiere_id');
    }

    public function factureDetails(): HasMany
    {
        return $this->hasMany(FactureComplexeDetail::class, 'matiere_id');
    }

    public function complexe(): HasOne
    {
        return $this->hasOne(MatiereComplexe::class, 'matiere_id');
    }

    public function provientDuComplexe()
    {
        return $this->complexe()->exists();
    }

    public function getPrixComplexeAttribute()
    {
        return $this->complexe ? $this->complexe->prix_complexe : null;
    }
}
