<?php

namespace App\Notifications;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TrialExpiringNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Workspace $workspace, public int $daysLeft) {}

    public function via(mixed $notifiable): array
    {
        $channels = [BrandaraDbChannel::class, 'mail'];

        try {
            if ($notifiable->pushSubscriptions()->exists()) {
                $channels[] = WebPushChannel::class;
            }
        } catch (\Throwable) {
        }

        return $channels;
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Your Brandara trial ends in {$this->daysLeft} ".($this->daysLeft === 1 ? 'day' : 'days'))
            ->greeting('Hi '.explode(' ', $notifiable->name)[0].',')
            ->line("Your free trial expires in **{$this->daysLeft} ".($this->daysLeft === 1 ? 'day' : 'days').'**.')
            ->line('Upgrade now to keep scheduling and publishing your posts without any interruption.')
            ->action('Upgrade my plan', url('/billing/upgrade'))
            ->line('Questions? Reply to this email — we respond within 24 hours.');
    }

    public function toWebPush(mixed $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title("Trial ends in {$this->daysLeft} ".($this->daysLeft === 1 ? 'day' : 'days'))
            ->body('Upgrade now to keep posting without interruption.')
            ->action('Upgrade', url('/billing/upgrade'))
            ->icon('/brandara-icon.svg');
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'trial_expiring',
            'title' => "Trial ends in {$this->daysLeft} ".($this->daysLeft === 1 ? 'day' : 'days'),
            'message' => 'Upgrade now to keep scheduling and publishing your posts.',
            'action_url' => '/billing/upgrade',
            'brand_id' => null,
        ];
    }
}
