<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProduitRecu;
use App\Models\Produit_fixes;
class ProduitRecu extends Model
{
    use HasFactory;
    protected $table='produit_recus';
    protected $primaryKey='code_produit';
    protected $fillable=[
        'pointeur','produit','prix','quantite'
    ];
    public function produit(){
        return $this->belongsTo(Produit_fixes::class,'code_produit','code_produit');
         }
}
?>