<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    protected $fillable = [
        'leave_category',
        'start_date',
        'end_date',
        'description',
        'bring_laptop',
        'still_be_contacted',
        'status',
        'submitted_by_user_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'bring_laptop' => 'boolean',
        'still_be_contacted' => 'boolean',
    ];

    // Relationships
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id', 'user_id');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('leave_category', $category);
    }

    // Accessors
    public function getDurationInDaysAttribute()
    {
        return Carbon::parse($this->start_date)->diffInDays(Carbon::parse($this->end_date)) + 1;
    }

    public function getIsActiveAttribute()
    {
        $now = Carbon::now();
        return $now->between($this->start_date, $this->end_date) && $this->getAttribute('status') === 'approved';
    }

    public function getStatusColorAttribute()
    {
        return match($this->getAttribute('status')) {
            'approved' => 'green',
            'rejected' => 'red',
            'pending' => 'yellow',
            default => 'gray'
        };
    }
}
