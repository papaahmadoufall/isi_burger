<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class command extends Model
{
    use HasFactory;
    protected $table = 'command';
    protected $fillable = [
        'user_id',
        'payment_id',
        'statut',
        'total',

    ];
    public function client() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    // Commande has one paiement
    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_id');
    }

    // Commande has multiple burgers through a pivot table
    public function burgers(): BelongsToMany
    {
        return $this->belongsToMany(Burger::class, 'burger')->withPivot('quantite')->withTimestamps();
    }
}
