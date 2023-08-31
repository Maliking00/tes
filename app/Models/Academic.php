<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Academic extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'academicYear',
        'academicSemester',
        'academicSystemDefault',
        'academicEvaluationStatus'
    ];
}
