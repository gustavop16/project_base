<?php

namespace App\Providers;

use App\Listeners\LogPasswordReset;
use App\Listeners\SendPasswordChangedNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if (request()->is('api/*')) {
            request()->headers->set('Accept', 'application/json');
        }

        Event::listen(PasswordReset::class, SendPasswordChangedNotification::class);
        Event::listen(PasswordReset::class, LogPasswordReset::class);
    }
}
