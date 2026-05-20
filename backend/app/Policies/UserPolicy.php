<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('users.viewAny');
    }

    public function view(User $user, User $model): bool
    {
        if ($user->id === $model->id) return true;
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('users.view')
            && $user->client_id === $model->client_id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('users.create');
    }

    public function update(User $user, User $model): bool
    {
        if ($user->id === $model->id) return true;
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('users.update')
            && $user->client_id === $model->client_id;
    }

    public function delete(User $user, User $model): bool
    {
        if ($user->id === $model->id) return false; // não pode se auto-excluir
        if ($user->hasRole('admin')) return true;

        return $user->hasPermissionTo('users.delete')
            && $user->client_id === $model->client_id;
    }
}
