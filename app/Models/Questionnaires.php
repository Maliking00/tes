<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Questionnaires extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'criterias_id',
        'academic_id',
        'questions'
    ];

    public function criteria()
    {
        return $this->belongsTo(Criterias::class, 'criterias_id');
    }
}
