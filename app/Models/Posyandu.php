<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $table = 'posyandu';

    protected $fillable = [
        'user_id',
        'nama_posyandu',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
