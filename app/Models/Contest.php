<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contest extends Model
{

    protected $fillable = [
        'contest_id',
        'info',
        'problems',
        'participants',
        'standings',
    ];

    protected $casts = [
        'info' => 'array',
        'problems' => 'array',
        'participants' => 'array',
        'standings' => 'array',
    ];

}
