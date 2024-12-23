<?php

namespace App\Observers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        if (!$user->roles()->exists()) {
            $defaultRole = Role::where('name', 'User')->first();

            if ($defaultRole) {
                $user->roles()->attach($defaultRole->id);
            }
        }
    }

    /**
     * Handle the User "updating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        if (Gate::denies('Edit user')) {
            abort(403, 'Unauthorized action.');
        }
        if (!empty($user->password)) {
            $user->password = Hash::make($user->password);
        } else {
            unset($user->password);
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleting(User $user): void
    {
        if (Gate::denies('Delete user')) {
            abort(403, 'Unauthorized action.');
        }
    }
    
    public function deleted(User $user): void
    {

    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }
}
