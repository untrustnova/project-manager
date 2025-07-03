<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $primaryKey = 'task_id';

    protected $fillable = [
        'project_id',
        'task_name',
        'description',
        'status',
        'priority',
        'assigned_user_id',
    ];

    // Relationships
    public function project()
    {
        return $this->belongsTo(Project::class, 'project_id', 'project_id');
    }

    public function assignedUser()
    {
        return $this->belongsTo(User::class, 'assigned_user_id', 'user_id');
    }

    // Helper methods
    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function isHighPriority()
    {
        return $this->priority === 'high';
    }

    public function getPriorityColor()
    {
        return match($this->priority) {
            'high' => 'danger',
            'medium' => 'warning',
            'low' => 'success',
            default => 'secondary'
        };
    }

    public function getStatusColor()
    {
        return match($this->status) {
            'completed' => 'success',
            'in_progress' => 'primary',
            'pending' => 'warning',
            'cancelled' => 'danger',
            default => 'secondary'
        };
    }
}
