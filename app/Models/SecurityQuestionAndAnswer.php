<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SecurityQuestionAndAnswer extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'question',
        'answer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
