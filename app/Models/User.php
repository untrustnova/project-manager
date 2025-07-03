<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'email',
        'password',
        'address',
        'phone_number',
        'birth',
        'telegram_link',
        'role',
        'image',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'birth' => 'date',
        'password' => 'hashed',
    ];

    // Relationships
    public function directedProjects()
    {
        return $this->hasMany(Project::class, 'project_director', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id', 'user_id');
    }

    public function assignedTasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id', 'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'submitted_by_user_id', 'user_id');
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isEmployee()
    {
        return $this->role === 'employee';
    }

    public function isHr()
    {
        return $this->role === 'hr';
    }
}
