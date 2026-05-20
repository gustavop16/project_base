<?php
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\UserPermissionController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

Route::get('/clear-cache', function () {
    Artisan::call('config:clear');
    Artisan::call('cache:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    return 'Cache limpo com sucesso!';
});

Route::get('/erro-teste', function () {
    throw new \RuntimeException('Erro forçado para testar o handler');
});

Route::post('/register',       [AuthController::class, 'register']);
Route::post('/login',          [AuthController::class, 'login']);
Route::post('/forgot-password',[PasswordResetController::class, 'forgotPassword']);
Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // ─── Usuários ─────────────────────────────────────────────────────────────
    Route::get   ('users',                   [UserController::class, 'index'])          ->middleware('permission:users.viewAny');
    Route::post  ('users',                   [UserController::class, 'store'])          ->middleware('permission:users.create');
    Route::get   ('users/{user}',            [UserController::class, 'show'])           ->middleware('permission:users.view');
    Route::put   ('users/{user}',            [UserController::class, 'update'])         ->middleware('permission:users.update');
    Route::patch ('users/{user}',            [UserController::class, 'update'])         ->middleware('permission:users.update');
    Route::delete('users/{user}',            [UserController::class, 'destroy'])        ->middleware('permission:users.delete');
    Route::post  ('users/{user}/photo',      [UserController::class, 'updatePhoto'])    ->middleware('permission:users.update');
    Route::put   ('users/{user}/password',   [UserController::class, 'updatePassword']) ->middleware('permission:users.update');

    // ─── Permissões avulsas de usuário ────────────────────────────────────────
    Route::get   ('users/{user}/permissions', [UserPermissionController::class, 'index'])  ->middleware('permission:users.view');
    Route::post  ('users/{user}/permissions', [UserPermissionController::class, 'store'])  ->middleware('permission:users.update');
    Route::delete('users/{user}/permissions', [UserPermissionController::class, 'destroy'])->middleware('permission:users.update');

    // ─── Permissões disponíveis (catálogo) ────────────────────────────────────
    Route::get('permissions', [PermissionController::class, 'index'])->middleware('permission:users.view');

    // ─── Anexos ───────────────────────────────────────────────────────────────
    Route::get   ('attachments/{model}/{item_id}',              [AttachmentController::class, 'index'])         ->middleware('permission:attachments.viewAny');
    Route::post  ('attachments',                                [AttachmentController::class, 'store'])         ->middleware('permission:attachments.create');
    Route::delete('attachments/{attachment}',                   [AttachmentController::class, 'destroy'])       ->middleware('permission:attachments.delete');
    Route::get   ('attachments/download/{model}/{item}/{file}', [AttachmentController::class, 'downloadOrView'])->middleware('permission:attachments.view');
});
