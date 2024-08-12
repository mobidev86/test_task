<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentAvailability extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'day_of_week',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
