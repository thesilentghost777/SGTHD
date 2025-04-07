<?php
// app/Models/Deli.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Deli extends Model
{
    protected $fillable = [
        'nom',
        'description',
        'montant',
    ];

    public function employes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'deli_user')
            ->withPivot('date_incident')
            ->withTimestamps();
    }
}
