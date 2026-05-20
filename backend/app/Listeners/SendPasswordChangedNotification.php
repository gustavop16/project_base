<?php

namespace App\Listeners;

use App\Models\User;
use App\Notifications\PasswordChangedNotification;
use Illuminate\Auth\Events\PasswordReset;

class SendPasswordChangedNotification
{
    public function handle(PasswordReset $event): void
    {
        /** @var User $user */
        $user = $event->user;
        $user->notify(new PasswordChangedNotification());
    }
}
