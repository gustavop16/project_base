<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    public function index(User $user): JsonResponse
    {
        $this->authorize('view', $user);

        return response()->json([
            'via_role'   => $user->getPermissionsViaRoles()->pluck('name'),
            'direct'     => $user->getDirectPermissions()->pluck('name'),
            'all'        => $user->getAllPermissions()->pluck('name'),
        ]);
    }

    public function store(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $request->validate([
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user->givePermissionTo($request->permissions);

        return response()->json([
            'message' => 'Permissões atribuídas com sucesso.',
            'direct'  => $user->getDirectPermissions()->pluck('name'),
        ]);
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $this->authorize('update', $user);

        $request->validate([
            'permissions'   => 'required|array|min:1',
            'permissions.*' => 'string|exists:permissions,name',
        ]);

        $user->revokePermissionTo($request->permissions);

        return response()->json([
            'message' => 'Permissões revogadas com sucesso.',
            'direct'  => $user->getDirectPermissions()->pluck('name'),
        ]);
    }
}
