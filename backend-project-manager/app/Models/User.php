<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use App\Models\Leave;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $primaryKey = 'id';

    public $otp_code;

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
        return $this->hasMany(Project::class, 'project_director', 'user_id');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'assigned_user_id', 'user_id');
    }

    public function leaves()
    {
        return $this->hasMany(Leave::class, 'submitted_by_user_id', 'user_id');
    }

    public function activities()
    {
        return $this->hasMany(Activity::class, 'user_id', 'user_id');
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
        $this->otp_code = rand(100000, 999999);
        $this->otp_expires_at = Carbon::now()->addMinutes(15);
        $this->save();

        return $this->otp_code;
    }

    public function verifyOTP($code)
    {
        if ($this->otp_code == $code && Carbon::now()->isBefore($this->otp_expires_at)) {
            $this->is_verified = true;
            $this->otp_code = null;
            $this->otp_expires_at = Carbon::now();
            $this->email_verified_at = Carbon::now();
            $this->save();
            return true;
        }
        return false;
    }
}