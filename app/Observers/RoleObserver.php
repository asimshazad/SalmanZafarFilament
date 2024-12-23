<?php

namespace App\Observers;

use App\Models\Role;
use Illuminate\Support\Facades\Gate;

class RoleObserver
{
    /**
     * Handle the Role "created" event.
     */
    public function created(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "updating" event.
     */
    public function updating(Role $role): void
    {
        if (Gate::denies('Edit role')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Handle the Role "updated" event.
     */
    public function updated(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "deleting" event.
     */
    public function deleting(Role $role): void
    {
        if (Gate::denies('Delete role')) {
            abort(403, 'Unauthorized action.');
        }
    }

    /**
     * Handle the Role "deleted" event.
     */
    public function deleted(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "restored" event.
     */
    public function restored(Role $role): void
    {
        //
    }

    /**
     * Handle the Role "force deleted" event.
     */
    public function forceDeleted(Role $role): void
    {
        //
    }
}
