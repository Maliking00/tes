<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Courses extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'courseName',
        'courseYearLevel',
        'courseSection'
    ];

    public function subjects()
    {
        return $this->hasMany(Subjects::class, 'course_id');
    }
}
