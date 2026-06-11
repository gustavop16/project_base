<?php

namespace App\Notifications;

use App\Models\Certificate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CertificateApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly Certificate $certificate) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = env('FRONTEND_URL', config('app.url')) . '/certificados';

        return (new MailMessage)
            ->subject('Certificado aprovado – ' . $this->certificate->certificateForm->name)
            ->greeting('Olá, ' . $notifiable->name . '!')
            ->line('O certificado abaixo foi **aprovado** com sucesso.')
            ->line('**Formulário:** ' . $this->certificate->certificateForm->name)
            ->line('**Navio:** ' . $this->certificate->vessel->name)
            ->line('**Aprovado por:** ' . ($this->certificate->approvedBy?->name ?? '—'))
            ->line('**Data de aprovação:** ' . $this->certificate->approved_at?->format('d/m/Y H:i'))
            ->action('Ver certificados', $url);
    }
}
