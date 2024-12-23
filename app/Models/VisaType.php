<?php

namespace App\Models;

use App\Observers\VisaTypeObserver;
use App\QueryBuilders\VisatypeBuilder;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

#[ObservedBy([VisaTypeObserver::class])]
class VisaType extends Model
{
    use HasFactory;

    protected $table = 'visa_types';

    protected $dates = [
        'created_at',
        'updated_at'
    ];

    protected $fillable = [
        'title',
        'country_id',
        'description',
        'status',
    ];

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }
}
