<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class Student extends Model
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'middle_name',
        'last_name',
        'email',
        'date_of_birth',
        'created_by',
    ];

    /**
     * Get the user that created the student record.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function availabilities()
    {
        return $this->hasMany(StudentAvailability::class);
    }

    /**
     * Get the mail address that should be used when sending notifications.
     *
     * @return string
     */
    public function routeNotificationForMail()
    {
        return $this->email; // Assuming your student model has an 'email' attribute
    }

    public function targetReports()
    {
        return $this->hasMany(TargetReport::class);
    }
}
