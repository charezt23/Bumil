<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Balita;
use App\Models\Posyandu;

class BalitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $posyandus = Posyandu::all();

        foreach ($posyandus as $posyandu) {
            Balita::factory()->count(5)->create(
                ['posyandu_id' => $posyandu->id]
            );
        }
    }
}