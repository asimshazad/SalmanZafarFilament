<?php

namespace App\Models;

use App\Observers\CountryObserver;
use App\Traits\CountryPhoto;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([CountryObserver::class])]
class Country extends Model
{
    use HasFactory,
    CountryPhoto; // i am using this trait for appending _URL, otherwise fileuploading handled automatically

    protected $table = 'countries';
    protected $fillable = [
        'name',
        'status',
        'country_photo'
    ];

    protected $appends = [
        'country_photo_url',
    ];

    public function visaTypes()
    {
        return $this->hasMany(VisaType::class)->where('visa_types.status', 1); // return only active visa types
    }

}
