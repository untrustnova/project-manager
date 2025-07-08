<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserStatsUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public array $stats;

    public function __construct(User $user)
    {
        $this->user = $user;
        // Siapkan data yang akan dikirim
        $this->stats = [
            'tasks_done' => $user->assignedTasks()->where('status', 'completed')->count(),
            'projects_total' => $user->directedProjects->count() + $user->assignedTasks->groupBy('project_id')->count(),
            'leaves_total' => $user->leaves->count(),
        ];
    }

    public function broadcastOn(): array
    {
        // Kirim ke channel privat milik user ini
        return [
            new PrivateChannel('user.'.$this->user->user_id),
        ];
    }

    public function broadcastWith(): array
    {
        // Data yang akan dikirim sebagai payload
        return ['stats' => $this->stats];
    }
}
