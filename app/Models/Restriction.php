<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restriction extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'academic_id',
        'teacher_id',
        'course_id',
        'subject_id',
        'teacher',
        'course',
        'subject'
    ];
}
