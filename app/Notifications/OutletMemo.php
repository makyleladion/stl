<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;


class OutletMemo extends Notification
{
    use Queueable;

    protected $memo, $announcer;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($memo, $announcer)
    {
        $this->memo = $memo;
        $this->announcer = $announcer;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'memo' => $this->memo,
            'announcer' => $this->announcer
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
