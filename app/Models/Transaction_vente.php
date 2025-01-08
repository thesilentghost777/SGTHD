<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction_vente extends Model
{
    use HasFactory;
    protected $fillable = [
        'produit',
        'serveur',
        'quantite',
        'prix',
        'total_ventes',
        'date_vente',
        'created_at',
        'updated_at'
    ];
    public function produit(){
        return $this->belongsTo(Produit_fixes::class,'code_produit','code_produit');
         }
         public function user()
    {
        return $this->belongsTo(User::class);
    }
}
