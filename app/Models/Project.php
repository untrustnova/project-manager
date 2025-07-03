<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_director',
        'project_name',
        'start_date',
        'deadline',
        'level',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'deadline' => 'date',
    ];

    // Relationships
    public function director()
    {
        return $this->belongsTo(User::class, 'project_director', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'project_id', 'project_id');
    }

    // Helper methods
    public function isOverdue()
    {
        return $this->deadline < now() && $this->status !== 'completed';
    }

    public function getProgressPercentage()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return 0;

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100);
    }
}
