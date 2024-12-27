<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produit_fixes;
class Production extends Model
{
    use HasFactory;
    protected $fillable = [
        'producteur',
        'produit',
        'quantite',
        'created_at',
        'updated_at'
    ];
    protected $table = 'Production';
 public function produit_fixe(){
    return $this->belongsTo(Produit_fixes::class,'code_produit','code_produit');
 }
}
