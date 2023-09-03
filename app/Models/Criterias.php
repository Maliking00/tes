<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Criterias extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'criterias'
    ];

    public function questionnaires()
    {
        return $this->hasMany(Questionnaires::class);
    }
}
