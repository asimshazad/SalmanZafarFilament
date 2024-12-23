<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory;

    protected $table = 'cart';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'itemable_id',
        'itemable_type',
        'quantity',
    ];

    /**
     * Get the owning itemable model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function itemable()
    {
        return $this->morphTo();
    }

    /**
     * Get the user that owns the cart.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
