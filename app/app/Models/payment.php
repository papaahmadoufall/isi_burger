<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class payment extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'id',
        'type',
    ];
    public function orders(): HasMany
    {
        return $this->hasMany(Command::class);
    }
}
