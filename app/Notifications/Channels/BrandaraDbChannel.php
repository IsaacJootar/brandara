<?php

namespace App\Notifications\Channels;

use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Writes to our custom notifications table (user_id-based, not Laravel's morph pattern).
 */
class BrandaraDbChannel
{
    public function send(mixed $notifiable, Notification $notification): void
    {
        $data = $notification->toArray($notifiable);

        DB::table('notifications')->insert([
            'id' => Str::uuid()->toString(),
            'user_id' => $notifiable->getKey(),
            'brand_id' => $data['brand_id'] ?? null,
            'type' => $data['type'] ?? class_basename($notification),
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? '',
            'action_url' => $data['action_url'] ?? null,
            'channels' => json_encode($data['channels'] ?? ['in_app']),
            'read_at' => null,
            'sent_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
