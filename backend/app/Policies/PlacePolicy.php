<?php

namespace App\Policies;

use App\Models\Place;
use App\Models\User;

class PlacePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('places.viewAny');
    }

    public function view(User $user, Place $place): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('places.view')
            && $user->client_id === $place->client_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('places.create');
    }

    public function update(User $user, Place $place): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('places.update')
            && $user->client_id === $place->client_id;
    }

    public function delete(User $user, Place $place): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('places.delete')
            && $user->client_id === $place->client_id;
    }
}
