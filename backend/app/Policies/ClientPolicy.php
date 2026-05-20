<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('clients.viewAny');
    }

    public function view(User $user, Client $client): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('clients.view')
            && $user->client_id === $client->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('clients.create');
    }

    public function update(User $user, Client $client): bool
    {
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('clients.update')
            && $user->client_id === $client->id;
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->hasPermissionTo('clients.delete'); // somente admin
    }
}
