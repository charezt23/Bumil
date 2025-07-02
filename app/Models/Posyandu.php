<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Posyandu extends Model
{
    protected $table = 'posyandu';

    protected $fillable = [
        'user_id',
        'nama_posyandu',
        'nama_desa'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function balita()
    {
        return $this->hasMany(balita::class, 'posyandu_id');
    }
}
