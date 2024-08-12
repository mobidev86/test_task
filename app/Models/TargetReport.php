<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TargetReport extends Model
{
    use HasFactory;

    protected $table = 'target_reports';

    protected $fillable = [
        'student_id',
        'start_date',
        'end_date',
        'target',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
