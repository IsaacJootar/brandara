<?php

namespace App\Notifications;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class PostFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post) {}

    public function via(mixed $notifiable): array
    {
        $channels = [BrandaraDbChannel::class];

        if (config('mail.default') !== 'log' || config('app.env') === 'production') {
            $channels[] = 'mail';
        }

        if (config('services.africastalking.key') && $notifiable->phone ?? false) {
            $channels[] = 'africastalking';
        }

        try {
            if ($notifiable->pushSubscriptions()->exists()) {
                $channels[] = WebPushChannel::class;
            }
        } catch (\Throwable) {
            // push_subscriptions table may not exist in test env
        }

        return $channels;
    }

    public function toMail(mixed $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your post didn\'t go live — here\'s what happened')
            ->greeting('Hi '.explode(' ', $notifiable->name)[0].',')
            ->line('One of your scheduled posts failed to publish.')
            ->line('**Reason:** '.($this->post->failure_reason ?? 'Unknown error.'))
            ->action('Fix it now', url("/{$this->post->brand->slug}/schedule"))
            ->line('Your post content is safe. You can retry or reschedule any time.');
    }

    public function toWebPush(mixed $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Post failed to publish')
            ->body($this->post->failure_reason ?? 'Tap to fix and retry.')
            ->action('Fix it', url("/{$this->post->brand->slug}/schedule"))
            ->icon('/brandara-icon.svg');
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'post_failed',
            'title' => 'Post failed to publish',
            'message' => $this->post->failure_reason ?? 'Your post could not be published.',
            'action_url' => "/{$this->post->brand->slug}/schedule",
            'brand_id' => $this->post->brand_id,
        ];
    }
}
