<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateRequestedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Certificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = env('FRONTEND_URL', config('app.url'))
            . '/certificados/' . $this->certificate->id . '/responder';

        return (new MailMessage)
            ->subject('Novo certificado para preencher – ' . $this->certificate->certificateForm->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('Um novo certificado foi solicitado e aguarda o seu preenchimento.')
            ->line('**Formulário:** ' . $this->certificate->certificateForm->name)
            ->line('**Navio:** ' . $this->certificate->vessel->name)
            ->line('**Solicitado por:** ' . ($this->certificate->createdBy?->name ?? '—'))
            ->action('Preencher formulário', $url)
            ->line('Por favor, acesse o link acima para responder ao formulário.');
    }
}
