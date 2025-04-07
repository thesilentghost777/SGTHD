<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    /**
     * Indique si l'ID est auto-incrémenté.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * Le type de données de l'ID du modèle.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Les attributs qui sont assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'type',
        'notifiable_type',
        'notifiable_id',
        'data',
        'read_at'
    ];

    /**
     * Les attributs qui doivent être convertis.
     *
     * @var array
     */
    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    /**
     * Obtenir l'entité notifiable.
     */
    public function notifiable()
    {
        return $this->morphTo();
    }

    /**
     * Marquer la notification comme lue.
     *
     * @return void
     */
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    /**
     * Déterminer si la notification a été lue.
     *
     * @return bool
     */
    public function read()
    {
        return $this->read_at !== null;
    }

    /**
     * Déterminer si la notification n'a pas été lue.
     *
     * @return bool
     */
    public function unread()
    {
        return $this->read_at === null;
    }
}
