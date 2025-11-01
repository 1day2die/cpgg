<?php

namespace App\Notifications;

use App\Settings\MailSettings;
use App\Settings\PterodactylSettings;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ServerSuspensionWarningNotification extends Notification
{

    protected $pterodactylSettings;
    protected $servers;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($servers)
    {
        $this->pterodactylSettings = app(PterodactylSettings::class);
        $this->servers = $servers;
    }

    /**
     * Format time remaining until suspension (similar to HomeController)
     */
    protected function formatTimeLeft($date)
    {
        if (!$date) return 'Unknown';

        $now = now();
        $daysLeft = $now->diffInDays($date, false);
        $hoursLeft = $now->diffInHours($date, false);
        $minutesLeft = $now->diffInMinutes($date, false);

        if ($daysLeft > 1) {
            return floor($daysLeft) . ' days';
        }

        if ($hoursLeft > 1) {
            return floor($hoursLeft) . ' hours';
        }

        if ($minutesLeft > 1) {
            return floor($minutesLeft) . ' minutes';
        }

        return 'Less than 1 minute';
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $channels = ['database'];

        $mailSettings = app(\App\Settings\MailSettings::class);

        if ($mailSettings->mail_from_address && $mailSettings->mail_host) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $sortedServers = $this->servers->sortBy(function ($serverData) {
            return $serverData['suspension_date']->timestamp;
        });

        $serverList = $sortedServers->map(function ($serverData, $index) {
            $server = $serverData['server'];
            $timeLeft = $this->formatTimeLeft($serverData['suspension_date']);
            $priorityIndicator = $index === 0 ? ' (Will be suspended first)' : '';
            return $server->name . ' (in ' . $timeLeft . ')' . $priorityIndicator;
        })->implode(', ');

        return (new MailMessage)
                    ->subject('Warning: Your servers will be suspended soon')
                    ->line('Your following server(s) will be suspended if you do not add sufficient credits to your account:')
                    ->line($serverList)
                    ->line('⚠️  **Important:** Servers will be suspended in the order shown above. If you don\'t add credits before the first server is billed, subsequent servers will also be suspended.')
                    ->line('To prevent suspension, please purchase more credits immediately.')
                    ->line('If you have any questions please let us know.')
                    ->action('Add Credits', route('home'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        // Sort servers by suspension time (earliest first)
        $sortedServers = $this->servers->sortBy(function ($serverData) {
            return $serverData['suspension_date']->timestamp;
        });

        $serverList = $sortedServers->map(function ($serverData, $index) {
            $server = $serverData['server'];
            $timeLeft = $this->formatTimeLeft($serverData['suspension_date']);
            $priorityIndicator = $index === 0 ? ' <strong>(Will be suspended first)</strong>' : '';
            return '<li>' . $server->name . ' (in ' . $timeLeft . ')' . $priorityIndicator . '</li>';
        })->implode('');

        return [
            'title' => 'Warning: Your servers will be suspended soon',
            'content' => '
                <h5>Warning: Your servers will be suspended soon</h5>
                <p>Your following server(s) will be suspended if you do not add sufficient credits to your account:</p>
                <ul>' . $serverList . '</ul>
                <p><strong>⚠️ Important:</strong> Servers will be suspended in the order shown above. If you don\'t add credits before the first server is billed, subsequent servers will also be suspended.</p>
                <p>To prevent suspension, please purchase more credits immediately.</p>
                <p>If you have any questions please let us know.</p>
                <p>Regards,<br />'.config('app.name', 'Laravel').'</p>
            ',
        ];
    }
}
