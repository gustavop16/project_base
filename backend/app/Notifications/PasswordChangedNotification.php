<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Sua senha foi alterada — ' . config('app.name'))
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('A senha da sua conta foi redefinida com sucesso.')
            ->line('Se você realizou essa alteração, pode ignorar este e-mail.')
            ->line('Se você **não** solicitou essa mudança, entre em contato com o suporte imediatamente.')
            ->salutation('Atenciosamente, equipe ' . config('app.name'));
    }
}
