<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleSession extends Model
{
    use HasFactory;

    protected $fillable = ['student_id', 'session_date', 'start_time', 'end_time', 'rating', 'is_repeated'];

    protected $casts = [
        'is_repeated' => 'boolean',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
