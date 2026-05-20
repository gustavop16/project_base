<?php

namespace App\Listeners;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Log;

class LogPasswordReset
{
    public function handle(PasswordReset $event): void
    {
        /** @var User $user */
        $user = $event->user;

        Log::info('Senha redefinida via link de recuperação', [
            'user_id' => $user->id,
            'email'   => $user->email,
            'ip'      => request()->ip(),
            'at'      => now()->toDateTimeString(),
        ]);
    }
}
