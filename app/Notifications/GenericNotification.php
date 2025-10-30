<?php

namespace App\Notifications;

use App\Models\NotificationTemplate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GenericNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected NotificationTemplate $template;

    protected array $data;

    protected array $channels;

    /**
     * Create a new notification instance.
     */
    public function __construct(NotificationTemplate $template, array $data, array $channels)
    {
        $this->template = $template;
        $this->data = $data;
        $this->channels = $channels;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable): array
    {
        return $this->channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable): MailMessage
    {
        $subject = $this->template->getEmailSubject($this->data);
        $body = $this->template->getEmailBody($this->data);

        $message = (new MailMessage)
            ->subject($subject)
            ->greeting('¡Hola!')
            ->line('Tienes una nueva notificación del sistema AppLSI:');

        // Agregar el cuerpo HTML
        $message->viewData = [
            'bodyHtml' => $body,
            'priority' => $this->template->priority,
            'priorityIcon' => $this->template->getPriorityIcon(),
            'priorityColor' => $this->template->getPriorityColor(),
        ];

        // Agregar acción si es crítico
        if ($this->template->isCritical()) {
            $message->action('Ver Detalles', url('/dashboard'));
            $message->line('⚠️ Esta es una notificación crítica que requiere tu atención inmediata.');
        }

        $message->line('Gracias por usar AppLSI.');

        return $message;
    }

    /**
     * Get the array representation of the notification (for database).
     */
    public function toArray($notifiable): array
    {
        return [
            'type' => $this->template->notification_type,
            'priority' => $this->template->priority,
            'priority_icon' => $this->template->getPriorityIcon(),
            'priority_color' => $this->template->getPriorityColor(),
            'subject' => $this->template->getEmailSubject($this->data),
            'message' => strip_tags($this->template->getEmailBody($this->data)),
            'data' => $this->data,
            'created_at' => now()->toIso8601String(),
        ];
    }

    /**
     * Get the SMS representation of the notification.
     * This requires a custom channel (will be implemented later).
     */
    public function toSms($notifiable): string
    {
        return $this->template->getSmsBody($this->data);
    }

    /**
     * Get the WhatsApp representation of the notification.
     * This requires a custom channel (will be implemented later).
     */
    public function toWhatsApp($notifiable): string
    {
        return $this->template->getWhatsAppBody($this->data);
    }
}
