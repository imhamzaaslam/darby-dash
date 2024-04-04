<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Auth\Passwords\TokenRepositoryInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Password;

class SendSetPasswordNotification extends Notification
{
    use Queueable;

    /**
     * The password token repository.
     *
     * @var TokenRepositoryInterface
     */
    protected TokenRepositoryInterface $tokens;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        $this->tokens = Password::getRepository();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable)
    {
        if (null === ($token = $this->createToken($notifiable))) {
            return;
        }

        $baseUrl = config('app.url');
        $email = $notifiable->getEmailForPasswordReset();
        $url = "{$baseUrl}/reset-password?token={$token}&email={$email}";

        return (new MailMessage)
            ->subject(Lang::get('Welcome!'))
            ->line(Lang::get('Welcome to ' . config('app.name')))
            ->line(Lang::get('You are receiving this email because we received a password reset request for your account.'))
            ->action(Lang::get('Set your password'), $url)
            ->line(Lang::get('This password reset link will expire in :count minutes.', ['count' => config('auth.passwords.' . config('auth.defaults.passwords') . '.expire')]));
    }

    private function createToken(User $user): ?string
    {
        if ($this->tokens->recentlyCreatedToken($user)) {
            return null;
        }

        return $this->tokens->create($user);
    }
}
