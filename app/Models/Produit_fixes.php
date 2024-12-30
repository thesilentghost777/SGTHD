<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produits_fixes;
class Produit_fixes extends Model
{
    use HasFactory;
    protected $table = 'Produit_fixes';
    protected $primaryKey='code_produit';
    protected $fillable = [
        'nom',
        'prix',
        'categorie',
        'created_at',
        'updated_at'
    ];
    public function productions(){
        return $this->hasMany(Production::class,'produit','code_produit');
    }
}
