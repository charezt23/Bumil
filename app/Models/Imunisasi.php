<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imunisasi extends Model
{
    protected $table = 'imunisasi';
    protected $fillable = [
        'balita_id',
        'jenis_imunisasi',
        'tanggal_imunisasi',
    ];

    public function balita()
    {
        return $this->belongsTo(Balita::class);
    }
}
