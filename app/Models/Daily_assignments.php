<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily_assignments extends Model
{
    protected $table = 'Daily_assignments';
    use HasFactory;

    protected $fillable = [
        'chef_production',
        'producteur',
        'produit',
        'expected_quantity',
        'assignment_date',
        'status'
    ];

    protected $casts = [
        'assignment_date' => 'date'
    ];

    public function produitFixe()
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }

    public function producteur()
    {
        return $this->belongsTo(User::class, 'producteur', 'id');
    }

    public function chefProduction()
    {
        return $this->belongsTo(User::class, 'chef_production', 'id');
    }
}
