<?php

namespace App\Notifications;

use App\Models\Certificate;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateFilledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Certificate $certificate,
        public readonly User $filledBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = env('FRONTEND_URL', config('app.url'))
            . '/certificados/' . $this->certificate->id . '/respostas';

        return (new MailMessage)
            ->subject('Certificado aguardando aprovação – ' . $this->certificate->certificateForm->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Um certificado foi preenchido e está aguardando a sua aprovação.')
            ->line('**Formulário:** ' . $this->certificate->certificateForm->name)
            ->line('**Navio:** ' . $this->certificate->vessel->name)
            ->line('**Preenchido por:** ' . $this->filledBy->name)
            ->action('Revisar respostas', $url)
            ->line('Acesse o link acima para aprovar ou reprovar o certificado.');
    }
}
