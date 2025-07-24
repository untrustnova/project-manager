<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $primaryKey = 'activity_id';

    protected $fillable = [
        'user_id',
        'activity_date',
        'check_in',
        'check_out',
        'status',
        'note'
    ];

    protected $casts = [
        'activity_date' => 'date',
        'check_in' => 'datetime:H:i:s',
        'check_out' => 'datetime:H:i:s',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->where('activity_date', Carbon::today());
    }

    public function scopeByDate($query, $date)
    {
        return $query->where('activity_date', $date);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    // Accessors
    public function getWorkingHoursAttribute()
    {
        if (!$this->check_in || !$this->check_out) {
            return null;
        }

        $checkIn = Carbon::parse($this->check_in);
        $checkOut = Carbon::parse($this->check_out);
        
        return $checkIn->diffInHours($checkOut, true);
    }

    public function getIsLateAttribute()
    {
        if (!$this->check_in) return false;
        
        $checkIn = Carbon::parse($this->check_in);
        $workStart = Carbon::parse('09:00:00'); // Assuming work starts at 9 AM
        
        return $checkIn->isAfter($workStart);
    }

    // Helper methods
    public function checkIn()
    {
        $this->check_in = Carbon::now();
        $this->status = $this->is_late ? 'late' : 'present';
        $this->save();
    }

    public function checkOut()
    {
        $this->check_out = Carbon::now();
        $this->save();
    }
}