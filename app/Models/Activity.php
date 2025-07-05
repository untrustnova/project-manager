<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'activities';
    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'user_id', 'activity_date', 'check_in',
        'check_out', 'status', 'note'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'check_in' => 'datetime',
        'check_out' => 'datetime'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function getDurationAttribute()
    {
        if (!$this->check_in || !$this->check_out) return null;
        return Carbon::parse($this->check_out)->diffInMinutes($this->check_in);
    }

    // Di App\Models\Activity.php
public function relatedTask()
{
    return $this->belongsTo(Task::class, 'related_task_id', 'task_id');
}

public function relatedProject()
{
    return $this->belongsTo(Project::class, 'related_project_id', 'project_id');
}
}
