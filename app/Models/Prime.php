<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prime extends Model
{
    use HasFactory;
    protected $table = 'Prime';
    protected $fillable = [
        'id_employe',
        'libelle',
        'montant'
    ];

    public function employe()
    {
        return $this->belongsTo(Employe::class, 'id_employe');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_employe');
    }
}
