<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'product_id',
        'interval',
        'stripe_plan',
        'price',
        'key_points',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'key_points' => 'array',
        ];
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
  
}
