<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class burger extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'image',
        'description',
        'stock',
    ];

    public function commands(): BelongsToMany
{
    return $this->belongsToMany(Command::class, 'command')->withPivot('quantite')->withTimestamps();
}
}
