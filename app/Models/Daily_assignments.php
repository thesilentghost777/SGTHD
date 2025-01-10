<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Daily_assignments extends Model
{
    protected $table = 'Daily_assignments';
    use HasFactory;

    public function produitFixe()
    {
        return $this->belongsTo(Produit_fixes::class, 'produit', 'code_produit');
    }
}
