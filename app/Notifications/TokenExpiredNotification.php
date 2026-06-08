<?php

namespace App\Notifications;

use App\Models\PlatformConnection;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class TokenExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public PlatformConnection $connection) {}

    private function platformName(): string
    {
        return ucfirst($this->connection->platform === 'twitter' ? 'X (Twitter)' : $this->connection->platform);
    }

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
        $platform = $this->platformName();
        $brandSlug = $this->connection->brand->slug;

        return (new MailMessage)
            ->subject("{$platform} disconnected — reconnect to keep posting")
            ->greeting('Hi '.explode(' ', $notifiable->name)[0].',')
            ->line("Your **{$platform}** connection has expired.")
            ->line('This means any posts scheduled for this platform will fail until you reconnect.')
            ->action('Reconnect now', url("/{$brandSlug}/connections"))
            ->line('It only takes 30 seconds to reconnect.');
    }

    public function toWebPush(mixed $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title($this->platformName().' disconnected')
            ->body('Reconnect to keep your posts going live automatically.')
            ->action('Reconnect', url("/{$this->connection->brand->slug}/connections"))
            ->icon('/brandara-icon.svg');
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'token_expired',
            'title' => $this->platformName().' disconnected',
            'message' => 'Your '.ucfirst($this->connection->platform).' connection has expired. Reconnect to keep posting.',
            'action_url' => "/{$this->connection->brand->slug}/connections",
            'brand_id' => $this->connection->brand_id,
        ];
    }
}
