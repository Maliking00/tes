<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentSubject extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'studentID',
        'subjectID'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
