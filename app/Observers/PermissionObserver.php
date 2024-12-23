<?php

namespace App\Observers;

use App\Models\Permission;
use Illuminate\Support\Facades\Gate;

class PermissionObserver
{
    /**
     * Handle the Permission "created" event.
     */
    public function created(Permission $permission): void
    {
        //
    }

    /**
     * Handle the Permission "updated" event.
     */
    public function updating(Permission $permission): void
    {
        if (Gate::denies('Edit permission')) {
            abort(403, 'Unauthorized action.');
        }
    }

    public function updated(Permission $permission): void
    {
        //
    }

    /**
     * Handle the Permission "deleted" event.
     */
    public function deleting(Permission $permission): void
    {
        if (Gate::denies('Delete permission')) {
            abort(403, 'Unauthorized action.');
        }
    }
    
    public function deleted(Permission $permission): void
    {
        //
    }

    /**
     * Handle the Permission "restored" event.
     */
    public function restored(Permission $permission): void
    {
        //
    }

    /**
     * Handle the Permission "force deleted" event.
     */
    public function forceDeleted(Permission $permission): void
    {
        //
    }
}
