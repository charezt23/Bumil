<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class balita extends Model
{
    protected $table = 'balita'; // Nama tabel di database
    protected $fillable = [
        'nama',
        'nik',
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
}
