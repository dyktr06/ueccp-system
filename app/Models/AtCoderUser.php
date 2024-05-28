<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AtCoderUser extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
    ];

    public function post()
    {
        return $this->belongsTo('App\Models\Post');
    }
}
