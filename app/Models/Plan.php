<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use LucasDotVin\Soulbscription\Models\Plan as ModelsPlan;

class Plan extends ModelsPlan
{
    use HasFactory;

    protected $fillable = [
        'grace_days',
        'name',
        'periodicity_type',
        'periodicity',
        'price',
    ];

    public function getNameWithPriceAttribute()
    {
        return $this->name . ' $'. $this->price;
    }
}
