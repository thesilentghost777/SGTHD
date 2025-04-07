<?php

namespace App\Notifications;

use App\Models\Message;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;

class SignalementNotification extends Notification
{
    use Queueable;

    protected $message;

    /**
     * Create a new notification instance.
     *
     * @param Message $message
     * @return void
     */
    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $sender = $this->message->name !== 'null' ? $this->message->name : 'Anonyme';

        // Corrigé: Utilisez $notifiable->email au lieu de $dg->email qui n'est pas défini ici
        Log::info('Préparation de l\'email de notification pour ' . $notifiable->email);

        return (new MailMessage)
            ->subject('Nouveau signalement reçu')
            ->greeting('Bonjour!')
            ->line('Un nouveau signalement a été reçu.')
            ->line('Message: ' . $this->message->message)
            ->line('Catégorie: ' . $this->message->type)
            ->line('Envoyé par: ' . $sender)
            ->line('Date: ' . $this->message->date_message->format('d/m/Y H:i'))
            ->action('Voir tous les messages', url('/lecture_message'))
            ->line('Merci d\'utiliser notre application!');
    }
}