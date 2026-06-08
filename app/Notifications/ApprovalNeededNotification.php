<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use NotificationChannels\WebPush\WebPushChannel;
use NotificationChannels\WebPush\WebPushMessage;

class ApprovalNeededNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Post $post, public User $submittedBy) {}

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
            ->subject('A post is waiting for your approval')
            ->greeting('Hi '.explode(' ', $notifiable->name)[0].',')
            ->line("**{$this->submittedBy->name}** has submitted a post for your review.")
            ->line(Str::limit($this->post->raw_input ?? '', 150))
            ->action('Review post', url("/{$this->post->brand->slug}/schedule"))
            ->line('Approve or request changes directly from your Schedule page.');
    }

    public function toWebPush(mixed $notifiable, mixed $notification): WebPushMessage
    {
        return (new WebPushMessage)
            ->title('Post awaiting approval')
            ->body($this->submittedBy->name.' submitted a post for review.')
            ->action('Review', url("/{$this->post->brand->slug}/schedule"))
            ->icon('/brandara-icon.svg');
    }

    public function toArray(mixed $notifiable): array
    {
        return [
            'type' => 'approval_needed',
            'title' => 'Post awaiting your approval',
            'message' => $this->submittedBy->name.' submitted a post for review.',
            'action_url' => "/{$this->post->brand->slug}/schedule",
            'brand_id' => $this->post->brand_id,
        ];
    }
}
