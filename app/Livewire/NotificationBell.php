<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    public int $unreadCount = 0;

    public bool $open = false;

    public function mount(): void
    {
        $this->unreadCount = $this->countUnread();
    }

    private function countUnread(): int
    {
        return DB::table('notifications')
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();
    }

    public function toggle(): void
    {
        $this->open = ! $this->open;

        if ($this->open) {
            $this->unreadCount = $this->countUnread();
        }
    }

    public function markAllRead(): void
    {
        DB::table('notifications')
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $this->unreadCount = 0;
    }

    public function markRead(string $notificationId): void
    {
        DB::table('notifications')
            ->where('id', $notificationId)
            ->where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
        $this->unreadCount = $this->countUnread();
    }

    #[On('echo:notifications.{authUserId},NotificationSent')]
    public function refreshCount(): void
    {
        $this->unreadCount = $this->countUnread();
    }

    public function getAuthUserIdProperty(): ?string
    {
        return auth()->id();
    }

    public function render()
    {
        $notifications = collect();

        if ($this->open) {
            $notifications = DB::table('notifications')
                ->where('user_id', auth()->id())
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();
        }

        return view('livewire.notification-bell', compact('notifications'));
    }
}
