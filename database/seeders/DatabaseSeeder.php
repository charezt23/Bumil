<?php

namespace Database\Seeders;

use App\Models\kunjungan_balita;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {


        User::factory()->create([
            'name' => 'Bidan Dua',
            'email' => 'bidandua@gmail.com',
        ]);

        User::factory()->create([
            'name' => 'Bidan Satu',
            'email' => 'bidansatu@gmail.com',
        ]);

        // Jalankan seeder untuk Posyandu, Balita, dan Kunjungan Balita
        $this->call([
            PosyanduSeeder::class,
            BalitaSeeder::class,
            KunjunganSeeder::class, // Pastikan ini sesuai dengan nama model yang benar
        ]);
    }
}
