<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Balita;

class kunjungan_balita extends Model
{
    use HasFactory;
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
        return $this->belongsTo(Balita::class, 'balita_id');
    }
}
