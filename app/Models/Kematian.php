<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kematian extends Model
{
    protected $table = 'kematian';
    protected $fillable = [
        'balita_id',
        'tanggal_kematian',
        'penyebab_kematian',
    ];

    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }
}
