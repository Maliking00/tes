<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluationList extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'restriction_id',
        'academic_id',
        'teacher_id',
        'course_id',
        'subject_id',
        'questionID',
        'teacher',
        'criteria',
        'question',
        'answer'
    ];
}
