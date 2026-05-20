<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    public function index(): JsonResponse
    {
        $permissions = Permission::orderBy('name')->pluck('name')->groupBy(function ($name) {
            return explode('.', $name)[0]; // agrupa por recurso (users, clients, etc.)
        });

        return response()->json($permissions);
    }
}
