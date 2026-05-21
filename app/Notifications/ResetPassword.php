<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Verify;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPassword extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(
        protected Verify $verify
    ){}

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via($notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable): MailMessage
    {

        $link = config('custom.reset_url');
        $query = http_build_query([
            'token' => encrypt($this->verify->token),
            'email' => $this->verify->email,
        ]);

        return (new MailMessage)
                    ->from('no-reply@myislandvisa.com', config('app.name'))
                    ->subject('Password Reset Request')
                    ->greeting('Hello ' . $notifiable->first_name . ',')
                    ->line('You\'ve requested for a password reset. Please Verify your profile by clicking the link below.')
                    ->action('Verify My Profile', $link . '?' . $query)
                    ->line('The link will expire on ' . $this->verify->expires_at->format('H:ia jS F, Y'))
                    ->line('If you have not requested the password reset, please contact us at hello@myislandvisa.com')
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array<string, mixed>
     */
    public function toArray($notifiable): array
    {
        return [
            //
        ];
    }
}
