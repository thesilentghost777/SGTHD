<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Extra extends Model
{
    use HasFactory;

    /**
     * Le nom de la table associée au modèle.
     *
     * @var string
     */
    protected $table = 'Extra';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<string>
     */
    protected $fillable = [
        'secteur',
        'heure_arriver_adequat',
        'heure_depart_adequat',
        'salaire_adequat',
        'interdit',
        'regles',
        'age_adequat'
    ];

    /**
     * Les attributs qui doivent être convertis en types natifs.
     *
     * @var array
     */
    protected $casts = [
        'heure_arriver_adequat' => 'datetime',
        'heure_depart_adequat' => 'datetime',
        'salaire_adequat' => 'decimal:2',
        'age_adequat' => 'integer',
    ];

    /**
     * Règles de validation pour les attributs du modèle.
     *
     * @return array
     */
    public static function rules()
    {
        return [
            'secteur' => 'required|string|max:50',
            'heure_arriver_adequat' => 'required|date_format:H:i',
            'heure_depart_adequat' => 'required|date_format:H:i|after:heure_arriver_adequat',
            'salaire_adequat' => 'required|numeric|min:0',
            'interdit' => 'nullable|string',
            'regles' => 'nullable|string',
            'age_adequat' => 'required|integer|min:16|max:70'
        ];
    }

    /**
     * Messages d'erreur personnalisés pour la validation.
     *
     * @return array
     */
    public static function messages()
    {
        return [
            'secteur.required' => 'Le secteur est obligatoire',
            'secteur.max' => 'Le secteur ne peut pas dépasser 50 caractères',
            'heure_arriver_adequat.required' => 'L\'heure d\'arrivée est obligatoire',
            'heure_arriver_adequat.date_format' => 'Le format de l\'heure d\'arrivée est invalide',
            'heure_depart_adequat.required' => 'L\'heure de départ est obligatoire',
            'heure_depart_adequat.date_format' => 'Le format de l\'heure de départ est invalide',
            'heure_depart_adequat.after' => 'L\'heure de départ doit être après l\'heure d\'arrivée',
            'salaire_adequat.required' => 'Le salaire est obligatoire',
            'salaire_adequat.numeric' => 'Le salaire doit être un nombre',
            'salaire_adequat.min' => 'Le salaire ne peut pas être négatif',
            'age_adequat.required' => 'L\'âge est obligatoire',
            'age_adequat.integer' => 'L\'âge doit être un nombre entier',
            'age_adequat.min' => 'L\'âge minimum est de 16 ans',
            'age_adequat.max' => 'L\'âge maximum est de 70 ans'
        ];
    }

    /**
     * Obtenir la durée de travail en heures
     *
     * @return float
     */
    public function getDureeTravailAttribute()
    {
        $debut = new \DateTime($this->heure_arriver_adequat);
        $fin = new \DateTime($this->heure_depart_adequat);
        $interval = $debut->diff($fin);
        return $interval->h + ($interval->i / 60);
    }

    /**
     * Obtenir le salaire horaire
     *
     * @return float
     */
    public function getSalaireHoraireAttribute()
    {
        return $this->salaire_adequat / $this->duree_travail;
    }

    /**
     * Vérifier si un âge est adéquat pour ce secteur
     *
     * @param int $age
     * @return bool
     */
    public function isAgeAdequat($age)
    {
        return $age >= $this->age_adequat;
    }

    /**
     * Convertir les interdits en tableau
     *
     * @return array
     */
    public function getInterditsArrayAttribute()
    {
        return $this->interdit ? array_map('trim', explode(',', $this->interdit)) : [];
    }

    /**
     * Convertir les règles en tableau
     *
     * @return array
     */
    public function getReglesArrayAttribute()
    {
        return $this->regles ? array_map('trim', explode(',', $this->regles)) : [];
    }
}
