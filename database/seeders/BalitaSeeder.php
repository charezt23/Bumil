<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\balita;
use App\Models\Posyandu;
use Carbon\Carbon;

class BalitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua posyandu yang ada
        $posyandus = Posyandu::all();

        // Data balita untuk setiap posyandu
        $balitaData = [
            // Posyandu 1 - Melati
            [
                ['nama' => 'Ahmad Fadli', 'nik' => '3201012301230001', 'jenis_kelamin' => 'L'],
                ['nama' => 'Siti Aisyah', 'nik' => '3201012301230002', 'jenis_kelamin' => 'P'],
                ['nama' => 'Budi Santoso', 'nik' => '3201012301230003', 'jenis_kelamin' => 'L']
            ],
            // Posyandu 2 - Mawar
            [
                ['nama' => 'Dewi Sartika', 'nik' => '3201012301230004', 'jenis_kelamin' => 'P'],
                ['nama' => 'Rizky Pratama', 'nik' => '3201012301230005', 'jenis_kelamin' => 'L'],
                ['nama' => 'Ayu Lestari', 'nik' => '3201012301230006', 'jenis_kelamin' => 'P']
            ],
            // Posyandu 3 - Anggrek
            [
                ['nama' => 'Bayu Permana', 'nik' => '3201012301230007', 'jenis_kelamin' => 'L'],
                ['nama' => 'Citra Maharani', 'nik' => '3201012301230008', 'jenis_kelamin' => 'P'],
                ['nama' => 'Dimas Saputra', 'nik' => '3201012301230009', 'jenis_kelamin' => 'L']
            ]
        ];

        // Alamat untuk setiap posyandu
        $alamatPerPosyandu = [
            [
                'Jl. Melati No. 1, RT 01/RW 01, Desa Sukamaju',
                'Jl. Melati No. 15, RT 02/RW 01, Desa Sukamaju', 
                'Jl. Melati No. 22, RT 01/RW 02, Desa Sukamaju'
            ],
            [
                'Jl. Mawar No. 5, RT 01/RW 01, Desa Sumber Rezeki',
                'Jl. Mawar No. 18, RT 02/RW 01, Desa Sumber Rezeki',
                'Jl. Mawar No. 30, RT 01/RW 02, Desa Sumber Rezeki'
            ],
            [
                'Jl. Anggrek No. 8, RT 01/RW 01, Desa Maju Bersama',
                'Jl. Anggrek No. 12, RT 02/RW 01, Desa Maju Bersama',
                'Jl. Anggrek No. 25, RT 01/RW 02, Desa Maju Bersama'
            ]
        ];

        foreach ($posyandus as $index => $posyandu) {
            for ($i = 0; $i < 3; $i++) {
                $data = $balitaData[$index][$i];
                
                balita::create([
                    'nama' => $data['nama'],
                    'nik' => $data['nik'],
                    'tanggal_lahir' => Carbon::now()->subMonths(rand(6, 24))->format('Y-m-d'), // Umur 6-24 bulan
                    'alamat' => $alamatPerPosyandu[$index][$i],
                    'jenis_kelamin' => $data['jenis_kelamin'],
                    'posyandu_id' => $posyandu->id,
                    'Buku_KIA' => rand(0, 1) ? 'ada' : 'tidak_ada' // Random ada/tidak ada
                ]);
            }
        }
    }
}
