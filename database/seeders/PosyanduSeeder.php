<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Posyandu;
use App\Models\User;
use Illuminate\Support\Str;

class PosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada user untuk dijadikan pemilik posyandu
        // Jika belum ada user, buat user dummy terlebih dahulu
        $users = User::all();
        if ($users->count() < 3) {
            // Buat user dummy jika belum ada
            for ($i = $users->count() + 1; $i <= 3; $i++) {
                User::create([
                    'name' => "User Posyandu $i",
                    'email' => "posyandu$i@example.com",
                    'password' => bcrypt('password123'),
                    'api_token' => Str::random(60)
                ]);
            }
            $users = User::all();
        }

        $posyanduData = [
            [
                'user_id' => $users[0]->id,
                'nama_posyandu' => 'Posyandu Melati',
                'nama_desa' => 'Desa Sukamaju'
            ],
            [
                'user_id' => $users[1]->id,
                'nama_posyandu' => 'Posyandu Mawar',
                'nama_desa' => 'Desa Sumber Rezeki'
            ],
            [
                'user_id' => $users[2]->id,
                'nama_posyandu' => 'Posyandu Anggrek',
                'nama_desa' => 'Desa Maju Bersama'
            ]
        ];

        foreach ($posyanduData as $data) {
            Posyandu::create($data);
        }
    }
}
