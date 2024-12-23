<?php

namespace App\Models;

use App\Observers\ProductObserver;
use App\Traits\ProductPhoto;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([ProductObserver::class])]
class Product extends Model
{
    use HasFactory, 
    ProductPhoto; // i am using this trait for appending _URL, otherwise fileuploading handled automatically

    protected $table = 'products';
    protected $fillable = [
        'name',
        'slug',
        'description',
        'product_photo',
        'price',
        'discount',
        'is_free',
        'status',
        'created_by_id'
    ];

    protected $appends = [
        'product_photo_url',
    ];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    protected $casts = [
        'is_free' => 'boolean',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];
}
