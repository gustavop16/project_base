<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $token) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = $this->resetUrl($notifiable);

        return (new MailMessage)
            ->subject('Redefinição de senha — ' . config('app.name'))
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Recebemos uma solicitação para redefinir a senha da sua conta.')
            ->action('Redefinir minha senha', $url)
            ->line('Este link expira em ' . config('auth.passwords.users.expire', 60) . ' minutos.')
            ->line('Se você não solicitou a redefinição de senha, nenhuma ação é necessária.')
            ->salutation('Atenciosamente, equipe ' . config('app.name'));
    }

    protected function resetUrl(object $notifiable): string
    {
        $frontend = rtrim(config('app.frontend_url', env('FRONTEND_URL', config('app.url'))), '/');
        $email    = urlencode($notifiable->getEmailForPasswordReset());

        return "{$frontend}/reset-password?token={$this->token}&email={$email}";
    }
}
