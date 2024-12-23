<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'payment_id',
        'status',
        'plan_id',
        'subscription_id',
        'user_id',
        'amount',
        'status',
        'response_json',
        'type',
        'method',
    ];

    protected $casts = [
        'response_json' => 'array',
    ];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'payment_products')->withTimestamps();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
