<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class balita extends Model
{
    use HasFactory;
    protected $table = 'balita'; // Nama tabel di database
    protected $fillable = [
        'nama',
        'nik',
        'nama_ibu',
        'tanggal_lahir',
        'alamat',
        'jenis_kelamin',
        'posyandu_id',
        'Buku_KIA'
    ];

    public function posyandu()
    {
        return $this->belongsTo(Posyandu::class, 'posyandu_id');
    }

    public function kunjungan_balita()
    {
        return $this->hasMany(kunjungan_balita::class, 'balita_id');
    }

    public function imunisasi()
    {
        return $this->hasMany(Imunisasi::class, 'balita_id');
    }
    
    public function kematian()
    {
        return $this->hasOne(Kematian::class, 'balita_id');
    }
}
