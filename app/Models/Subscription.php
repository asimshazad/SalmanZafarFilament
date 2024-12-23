<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    use HasFactory;

    protected $casts = [
        'ends_at' => 'datetime:Y-m-d h:i:s',
     ];

    protected $dates = [
        
        'trial_ends_at',
        'created_at'
    ];


    protected $fillable = [
        'user_id',
        'type',
        'plan_id',
        'stripe_id',
        'stripe_status',
        'stripe_price',
        'quantity',
        'trial_ends_at',
        'ends_at',
        'invoice'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }


}
