<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\kunjungan_balita;
use App\Models\Balita;

class KunjunganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $balitas = Balita::all();

        $balitasbyPosyandu = $balitas->groupBy('posyandu_id');
        foreach ($balitasbyPosyandu as $posyandu_id => $balitasInPosyandu) {
            $tanggalKunjungan = now()->subDays(rand(1, 30));
            foreach ($balitasInPosyandu as $balita) {
                kunjungan_balita::factory()->count(1)->create([
                    'balita_id' => $balita->id,
                    'tanggal_kunjungan' => $tanggalKunjungan,
                ]);
            }
        }

        foreach ($balitasbyPosyandu as $posyandu_id => $balitasInPosyandu) {
            $tanggalKunjungan = now();
            foreach ($balitasInPosyandu as $balita) {
                kunjungan_balita::factory()->count(1)->create([
                    'balita_id' => $balita->id,
                    'tanggal_kunjungan' => $tanggalKunjungan,
                ]);
            }
        }


    }
}
