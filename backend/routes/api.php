<?php
use App\Http\Controllers\Api\Auth\PasswordResetController;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\PermissionController;
use App\Http\Controllers\Api\AttachmentController;
use App\Http\Controllers\Api\UserPermissionController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VesselController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\CertificateFormController;
use App\Http\Controllers\Api\CertificateFormResponseController;
use App\Http\Controllers\Api\CertificateController;
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

Route::get('certificates/verify/{token}', [CertificateController::class, 'verify']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // ─── Perfil do próprio usuário ────────────────────────────────────────────
    Route::get  ('profile', [UserController::class, 'showProfile']);
    Route::patch('profile', [UserController::class, 'updateProfile']);

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

    // ─── Navios ───────────────────────────────────────────────────────────────
    Route::get   ('vessels',          [VesselController::class, 'index'])  ->middleware('permission:vessel.viewAny');
    Route::post  ('vessels',          [VesselController::class, 'store'])  ->middleware('permission:vessel.create');
    Route::get   ('vessels/{vessel}', [VesselController::class, 'show'])   ->middleware('permission:vessel.view');
    Route::put   ('vessels/{vessel}', [VesselController::class, 'update']) ->middleware('permission:vessel.update');
    Route::patch ('vessels/{vessel}', [VesselController::class, 'update']) ->middleware('permission:vessel.update');
    Route::delete('vessels/{vessel}', [VesselController::class, 'destroy'])->middleware('permission:vessel.delete');

    // ─── Perguntas ────────────────────────────────────────────────────────────
    Route::get   ('questions',            [QuestionController::class, 'index'])  ->middleware('permission:question.viewAny');
    Route::post  ('questions',            [QuestionController::class, 'store'])  ->middleware('permission:question.create');
    Route::get   ('questions/{question}', [QuestionController::class, 'show'])   ->middleware('permission:question.view');
    Route::put   ('questions/{question}', [QuestionController::class, 'update']) ->middleware('permission:question.update');
    Route::patch ('questions/{question}', [QuestionController::class, 'update']) ->middleware('permission:question.update');
    Route::delete('questions/{question}', [QuestionController::class, 'destroy'])->middleware('permission:question.delete');

    // ─── Formulários de Certificado ───────────────────────────────────────────
    Route::get   ('certificate-forms',                    [CertificateFormController::class, 'index'])  ->middleware('permission:certificate_form.viewAny');
    Route::post  ('certificate-forms',                    [CertificateFormController::class, 'store'])  ->middleware('permission:certificate_form.create');
    Route::get   ('certificate-forms/{certificateForm}',  [CertificateFormController::class, 'show'])   ->middleware('permission:certificate_form.view');
    Route::put   ('certificate-forms/{certificateForm}',  [CertificateFormController::class, 'update']) ->middleware('permission:certificate_form.update');
    Route::patch ('certificate-forms/{certificateForm}',  [CertificateFormController::class, 'update']) ->middleware('permission:certificate_form.update');
    Route::delete('certificate-forms/{certificateForm}',  [CertificateFormController::class, 'destroy'])->middleware('permission:certificate_form.delete');

    // ─── Certificados ────────────────────────────────────────────────────────
    Route::get   ('certificates',                         [CertificateController::class, 'index'])         ->middleware('permission:certificates.viewAny');
    Route::post  ('certificates',                         [CertificateController::class, 'store'])         ->middleware('permission:certificates.create');
    Route::get   ('certificates/{certificate}',           [CertificateController::class, 'show'])          ->middleware('permission:certificates.view');
    Route::delete('certificates/{certificate}',           [CertificateController::class, 'destroy'])       ->middleware('permission:certificates.delete');
    Route::get   ('certificates/{certificate}/form',      [CertificateController::class, 'getForm'])       ->middleware('permission:certificates.view');
    Route::get   ('certificates/{certificate}/response',  [CertificateController::class, 'getResponse'])   ->middleware('permission:certificates.view');
    Route::get   ('certificates/{certificate}/history',   [CertificateController::class, 'history'])        ->middleware('permission:certificates.view');
    Route::post  ('certificates/{certificate}/respond',   [CertificateController::class, 'submitResponse'])->middleware('permission:form_response.create');
    Route::post  ('certificates/{certificate}/approve',   [CertificateController::class, 'approve'])       ->middleware('permission:certificates.approve');
    Route::post  ('certificates/{certificate}/reject',    [CertificateController::class, 'reject'])        ->middleware('permission:certificates.approve');
    Route::get   ('certificates/{certificate}/pdf',       [CertificateController::class, 'downloadPdf'])   ->middleware('permission:certificates.view')->name('certificates.pdf');

    // ─── Respostas de Formulários ─────────────────────────────────────────────
    Route::get ('form-responses',                              [CertificateFormResponseController::class, 'index']) ->middleware('permission:form_response.viewAny');
    Route::post('form-responses',                              [CertificateFormResponseController::class, 'store']) ->middleware('permission:form_response.create');
    Route::get ('form-responses/{certificateFormResponse}',    [CertificateFormResponseController::class, 'show'])  ->middleware('permission:form_response.view');

    // ─── Anexos ───────────────────────────────────────────────────────────────
    Route::get   ('attachments/{model}/{item_id}',              [AttachmentController::class, 'index'])         ->middleware('permission:attachments.viewAny');
    Route::post  ('attachments',                                [AttachmentController::class, 'store'])         ->middleware('permission:attachments.create');
    Route::delete('attachments/{attachment}',                   [AttachmentController::class, 'destroy'])       ->middleware('permission:attachments.delete');
    Route::get   ('attachments/download/{model}/{item}/{file}', [AttachmentController::class, 'downloadOrView'])->middleware('permission:attachments.view');
});
