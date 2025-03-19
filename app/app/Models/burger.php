<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class burger extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'prix',
        'image',
        'description',
        'stock',
    ];

}
return $this->belongsToMany(Command::class, 'command')->withPivot('quantite')->withTimestamps();
