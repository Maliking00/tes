<?php

namespace App\Models;

use App\Traits\Uuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;
    use Uuid;

    protected $fillable = [
        'semaphoreApiKey',
        'smsMode',
        'weatherCity'
    ];
}
