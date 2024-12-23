<?php

namespace App\Observers;

use App\Models\Country;
use Illuminate\Support\Facades\Gate;

class CountryObserver
{
    /**
     * Handle the Country "created" event.
     */
    public function created(Country $country): void
    {
        //
    }

    public function updating(Country $country)
    {
        if (Gate::denies('Edit country')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Handle the Country "deleted" event.
     */
    public function deleting(Country $country): void
    {
        if (Gate::denies('Delete country')) {
            abort(403, 'Unauthorized action.');
        }
        if ($country->country_photo) {
            $country->deleteCountryPhoto();
        }
    }
    /**
     * Handle the Country "updated" event.
     */
    public function updated(Country $country): void
    {
        //
    }

    /**
     * Handle the Country "deleted" event.
     */
    public function deleted(Country $country): void
    {
        //
    }

    /**
     * Handle the Country "restored" event.
     */
    public function restored(Country $country): void
    {
        //
    }

    /**
     * Handle the Country "force deleted" event.
     */
    public function forceDeleted(Country $country): void
    {
        //
    }
}
