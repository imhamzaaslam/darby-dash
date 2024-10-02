<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class GenericNotification extends Notification
{
    use Queueable;

    protected $template, $settings, $sender, $receiver;

    public function __construct(array $template, array $settings, $sender, $receiver)
    {
        $this->template = $template;
        $this->settings = $settings;
        $this->sender = $sender;
        $this->receiver = $receiver;
    }

   /**
     * Get the notification's delivery channels.
     *
     * @param  object  $notifiable
     * @return array
     */
    public function via(object $notifiable): array
    {
        if ($this->settings['deliverable_channel'] === 'database') {
            return ['database'];
        }
        if ($this->settings['deliverable_channel'] === 'mail') {
            return ['mail'];
        }

        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param object $notifiable
     */
    public function toMail(object $notifiable)
    {
        return (new MailMessage)
            ->subject($this->template['title'])
            ->from($this->sender->email)
            ->cc($this->receiver->email)
            ->view('emails.notification_template', ['template' => $this->template, 'sender' => $this->sender, 'receiver' => $this->receiver]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param object $notifiable
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => $this->template['title'],
            'message' => $this->template['message'],
            'url' => $this->template['url'],
            'type' => $this->template['type'],
            'sender_id' => $this->sender->id,
        ];
    }
}
