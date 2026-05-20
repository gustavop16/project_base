<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public string $plainPassword) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $loginUrl = rtrim(env('FRONTEND_URL', config('app.url')), '/') . '/login';

        return (new MailMessage)
            ->subject('Bem-vindo ao ' . config('app.name') . ' — suas credenciais de acesso')
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Sua conta foi criada com sucesso. Utilize as credenciais abaixo para acessar o sistema:')
            ->line('**E-mail:** ' . $notifiable->email)
            ->line('**Senha:** ' . $this->plainPassword)
            ->action('Acessar o sistema', $loginUrl)
            ->line('Por segurança, recomendamos alterar sua senha no primeiro acesso.')
            ->salutation('Atenciosamente, equipe ' . config('app.name'));
    }
}
