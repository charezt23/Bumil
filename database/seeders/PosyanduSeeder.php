<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Posyandu;
use App\Models\User;

class PosyanduSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            $romawi = ['I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X'];
            $user_id = $user->id;
            foreach ($romawi as $r) {
                Posyandu::create([
                    'user_id' => $user_id,
                    'nama_posyandu' => ($user_id === 1) ? 'Posyandu Melati ' . $r : 'Posyandu Mawar ' . $r,
                    'nama_desa' => ($user_id === 1) ? 'Desa Bajing' : 'Desa Bajing Kulon',
                ]);
            }
        }
    }
}
