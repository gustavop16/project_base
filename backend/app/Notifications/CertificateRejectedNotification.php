<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateRejectedNotification extends Notification implements ShouldQueue
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
            ->subject('Certificado reprovado – ' . $this->certificate->certificateForm->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O certificado abaixo foi **reprovado** e precisa ser corrigido.')
            ->line('**Formulário:** ' . $this->certificate->certificateForm->name)
            ->line('**Navio:** ' . $this->certificate->vessel->name)
            ->line('**Reprovado por:** ' . ($this->certificate->rejectedBy?->name ?? '—'))
            ->line('**Motivo:** ' . ($this->certificate->rejected_reason ?? '—'))
            ->action('Corrigir e reenviar', $url)
            ->line('Por favor, acesse o link acima, corrija as informações e reenvie o formulário.');
    }
}
