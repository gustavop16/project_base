<?php

namespace App\Policies;

use App\Models\Planning;
use App\Models\User;

class PlanningPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('plannings.viewAny');
    }

    public function view(User $user, Planning $planning): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('plannings.view')
            && $user->client_id === $planning->client_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('plannings.create');
    }

    public function update(User $user, Planning $planning): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('plannings.update')
            && $user->client_id === $planning->client_id;
    }

    public function delete(User $user, Planning $planning): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('plannings.delete')
            && $user->client_id === $planning->client_id;
    }

    public function updateStatus(User $user, Planning $planning): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('plannings.updateStatus')
            && $user->client_id === $planning->client_id;
    }
}
