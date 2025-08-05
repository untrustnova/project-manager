<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use App\Models\Leave;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    // Ensure 'role' is a fillable attribute and exists in the database table
    protected $fillable = [
        'name',
        'email',
        'password',
        'telegram_link',
        'birthdate',
        'address',
        'phone_number',
        'division',
        'role',
        'status',
        'otp_code',
        'otp_expires_at',
        'is_verified',
        'image',
        'pendidikan_terakhir',
        'tanggal_masuk',
        'last_otp_verification',
    ];

    /**
     * The possible division values
     */
    public const DIVISIONS = [
        'UI/UX',
        'Frontend Web',
        'Backend Web',
        'Android Developer',
        'iOS Developer',
        'Content Creator',
        'Copywriter',
        'Tester'
    ];

    /**
     * Get the user's division with proper formatting
     */
    public function getDivisionAttribute($value)
    {
        return $value ? ucwords(str_replace('_', ' ', $value)) : null;
    }

    /**
     * Set the user's division
     */
    public function setDivisionAttribute($value)
    {
        $this->attributes['division'] = $value ? strtolower(str_replace(' ', '_', $value)) : null;
    }

    // Optionally, you can add an accessor if you want to customize how 'role' is retrieved
    public function getRoleAttribute($value)
    {
        return $value;
    }

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'birthdate' => 'date',
        'is_verified' => 'boolean',
        'password' => 'hashed',
    ];

    // Relationships
    public function projectsAsDirector()
    {
        return $this->hasMany(Project::class, 'project_director');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'submitted_by_user_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id');
    }

    // Scopes
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeByDivision($query, $division)
    {
        return $query->where('division', $division);
    }

    /**
     * Get users by multiple divisions
     */
    public function scopeByDivisions($query, array $divisions)
    {
        return $query->whereIn('division', $divisions);
    }

    // Accessors & Mutators
    public function getAgeAttribute()
    {
        return $this->birthdate ? Carbon::parse((string) $this->birthdate)->age : null;
    }

    // Helper methods
    public function isAdmin()
    {
        return $this->getAttribute('role') === 'admin';
    }

    public function isHR()
    {
        return $this->getAttribute('role') === 'hr';
    }

    public function generateOTP()
    {
        $otp = rand(100000, 999999);
        $now = Carbon::now()->timezone('Asia/Jakarta');

        $this->forceFill([
            'otp_code' => $otp,
            'otp_expires_at' => $now->addMinutes(15)
        ])->save();

        Log::info('Generated new OTP', [
            'email' => $this->email,
            'otp' => $otp,
            'expires_at' => $this->otp_expires_at->toDateTimeString(),
            'current_time' => $now->toDateTimeString()
        ]);

        return $otp;
    }

    public function verifyOTP($code)
    {
        $now = Carbon::now()->timezone('Asia/Jakarta');
        $expiresAt = $this->otp_expires_at;

        Log::info('OTP Verification attempt', [
            'email' => $this->email,
            'provided_code' => $code,
            'stored_code' => $this->getAttribute('otp_code'),
            'now' => $now->toDateTimeString(),
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : null,
            'last_verification' => $this->last_otp_verification,
            'is_expired' => $expiresAt ? $now->isAfter($expiresAt) : true,
            'code_matches' => $this->getAttribute('otp_code') == $code
        ]);

        // Fresh query to ensure we have the latest OTP data
        $this->refresh();

        if ($this->getAttribute('otp_code') == $code && $expiresAt && $now->isBefore($expiresAt)) {
            $this->forceFill([
                'is_verified' => true,
                'otp_code' => null,
                'otp_expires_at' => $now,
                'email_verified_at' => $now,
                'last_otp_verification' => $now
            ])->save();
            return true;
        }
        return false;
    }

    /**
     * Check if user needs OTP verification
     * Returns true if:
     * 1. User is not verified, or
     * 2. Last OTP verification was more than 5 hours ago
     */
    public function needsOTPVerification(): bool
    {
        if (!$this->is_verified) {
            return true;
        }

        if (!$this->last_otp_verification) {
            return true;
        }

        return Carbon::parse($this->last_otp_verification)->addHours(5)->isPast();
    }
}