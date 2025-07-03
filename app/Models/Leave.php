<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Leave extends Model
{
    use HasFactory;

    protected $primaryKey = 'leave_id';

    protected $fillable = [
        'leave_category',
        'start_date',
        'end_date',
        'description',
        'bring_laptop',
        'still_be_contacted',
        'submitted_by_user_id',
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

    // Helper methods
    public function getDurationInDays()
    {
        $startDate = Carbon::parse($this->start_date);
        $endDate = Carbon::parse($this->end_date);

        return $endDate->diffInDays($startDate) + 1;
    }

    public function isActive()
    {
        $now = now();
        return $now->between($this->start_date, $this->end_date);
    }

    public function isPending()
    {
        return $this->start_date > now();
    }

    public function isExpired()
    {
        return $this->end_date < now();
    }
}
