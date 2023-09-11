<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeacherEvaluationStatus extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'restriction_id',
        'evaluator_id',
        'academic_id',
        'teacher_id',
        'course_id',
        'subject_id',
        'teacher',
        'course',
        'subject'
    ];
}
