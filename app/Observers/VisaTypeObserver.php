<?php

namespace App\Observers;

use App\Models\VisaType;
use Illuminate\Support\Facades\Gate;

class VisaTypeObserver
{
    /**
     * Handle the VisaType "created" event.
     */
    public function created(VisaType $visaType): void
    {
        //
    }

    /**
     * Handle the VisaType "updating" event.
     */
    public function updating(VisaType $visaType): void
    {
        if (Gate::denies('Edit visatype')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Handle the VisaType "updated" event.
     */
    public function updated(VisaType $visaType): void
    {
        //
    }

    /**
     * Handle the VisaType "deleting" event.
     */
    public function deleting(VisaType $visaType): void
    {
        if (Gate::denies('Delete visatype')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Handle the VisaType "deleted" event.
     */
    public function deleted(VisaType $visaType): void
    {
        //
    }

    /**
     * Handle the VisaType "restored" event.
     */
    public function restored(VisaType $visaType): void
    {
        //
    }

    /**
     * Handle the VisaType "force deleted" event.
     */
    public function forceDeleted(VisaType $visaType): void
    {
        //
    }
}
