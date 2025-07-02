<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kunjungan_balita extends Model
{
    protected $table = 'kunjungan_balita'; // Nama tabel di database

    protected $fillable = [
        'balita_id',
        'tanggal_kunjungan',
        'berat_badan',
        'tinggi_badan',
        'Status_gizi',
        'rambu_gizi',
    ];

    public function balita()
    {
        return $this->belongsTo('App\Models\balita', 'balita_id');
    }
}
