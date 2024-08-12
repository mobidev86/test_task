<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Carbon\Carbon;

class StudentSessionReminder extends Notification
{
    use Queueable;

    protected $session;

    public function __construct($session)
    {
        $this->session = $session;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        
        $session = $this->session;
        return (new MailMessage)
            ->subject('Session Reminder')
            ->line('You have a session scheduled soon.')
            ->line('Session Date: ' . Carbon::parse($session->session_date)->format('M d, Y'))
            ->line('Start Time: ' . Carbon::parse($session->start_time)->format('H:i'))
            ->line('Please ensure you join the session on time.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
