<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliUser extends Model
{
    protected $table = 'deli_user';

    protected $fillable = [
        'deli_id',
        'user_id',
        'date_incident'
    ];

    /**
     * Get the user associated with the incident.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the deli (delit/incident) associated with this record.
     */
    public function deli()
    {
        return $this->belongsTo(Deli::class);
    }
}
