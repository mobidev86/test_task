<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportTemplate extends Model
{
    use HasFactory;

    // The table associated with the model.
    protected $table = 'report_templates';

    // The attributes that are mass assignable.
    protected $fillable = [
        'template',
    ];

    // The attributes that should be hidden for arrays.
    protected $hidden = [];
}
