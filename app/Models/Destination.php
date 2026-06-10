<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Destination extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'description',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(TourPackage::class);
    }
}
