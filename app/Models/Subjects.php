<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subjects extends Model
{
    use HasFactory;
    use Uuid;
    protected $fillable = [
        'course_id',
        'subjectCode',
        'subjectName',
        'subjectDescription'
    ];

    public function course()
    {
        return $this->belongsTo(Courses::class, 'course_id');
    }
}
