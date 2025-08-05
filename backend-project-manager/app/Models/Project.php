<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_name',
        'start_date',
        'deadline',
        'project_director',
        'level',
        'status'
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

    // Scopes
    public function scopeOngoing($query)
    {
        return $query->where('status', 'ongoing');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('deadline', '<', Carbon::now())->where('status', '!=', 'completed');
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return Carbon::now()->isAfter(Carbon::parse((string) $this->deadline)) && $this->getAttribute('status') !== 'completed';
    }

    public function getCompletionPercentageAttribute()
    {
        $totalTasks = $this->tasks()->count();
        if ($totalTasks === 0) return 0;

        $completedTasks = $this->tasks()->where('status', 'completed')->count();
        return round(($completedTasks / $totalTasks) * 100, 2);
    }

    public function getDaysRemainingAttribute()
    {
        return Carbon::now()->diffInDays(Carbon::parse((string) $this->deadline), false);
    }
}
