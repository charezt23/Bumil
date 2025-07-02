<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\kunjungan_balita;
use App\Models\balita;
use Carbon\Carbon;

class KunjunganBalitaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ambil semua balita
        $balitas = balita::all();

        // Status gizi options
        $statusGizi = ['N', 'K', 'T']; // Normal, Kurang, Tinggi
        $rambuGizi = ['O', 'N1', 'N2', 'T1', 'T2', 'T3']; // Orange, Normal1, Normal2, Tinggi1, Tinggi2, Tinggi3

        // Untuk setiap balita, buat 2 kunjungan
        foreach ($balitas as $balita) {
            // Hitung umur balita dalam bulan
            $umurBulan = Carbon::parse($balita->tanggal_lahir)->diffInMonths(Carbon::now());
            
            // Kunjungan pertama (1-2 bulan yang lalu)
            $tanggalKunjungan1 = Carbon::now()->subMonths(rand(1, 2))->format('Y-m-d');
            
            // Estimasi berat dan tinggi berdasarkan umur untuk kunjungan pertama
            $beratAwal = $this->estimateWeight($umurBulan - 1);
            $tinggiAwal = $this->estimateHeight($umurBulan - 1);
            
            kunjungan_balita::create([
                'balita_id' => $balita->id,
                'tanggal_kunjungan' => $tanggalKunjungan1,
                'berat_badan' => $beratAwal,
                'tinggi_badan' => $tinggiAwal,
                'Status_gizi' => $statusGizi[array_rand($statusGizi)],
                'rambu_gizi' => $rambuGizi[array_rand($rambuGizi)]
            ]);

            // Kunjungan kedua (lebih baru, 0-1 bulan yang lalu)
            $tanggalKunjungan2 = Carbon::now()->subDays(rand(0, 30))->format('Y-m-d');
            
            // Berat dan tinggi untuk kunjungan kedua (sedikit bertambah)
            $beratSekarang = $beratAwal + rand(200, 800) / 1000; // Tambah 0.2-0.8 kg
            $tinggiSekarang = $tinggiAwal + rand(1, 3); // Tambah 1-3 cm
            
            kunjungan_balita::create([
                'balita_id' => $balita->id,
                'tanggal_kunjungan' => $tanggalKunjungan2,
                'berat_badan' => $beratSekarang,
                'tinggi_badan' => $tinggiSekarang,
                'Status_gizi' => $statusGizi[array_rand($statusGizi)],
                'rambu_gizi' => $rambuGizi[array_rand($rambuGizi)]
            ]);
        }
    }

    /**
     * Estimasi berat badan berdasarkan umur (dalam kg)
     */
    private function estimateWeight($umurBulan)
    {
        // Berat lahir rata-rata 3.2 kg, bertambah sekitar 0.6 kg per bulan
        $beratEstimasi = 3.2 + ($umurBulan * 0.6);
        
        // Tambahkan variasi random ±20%
        $variasi = $beratEstimasi * (rand(-20, 20) / 100);
        
        return round($beratEstimasi + $variasi, 2);
    }

    /**
     * Estimasi tinggi badan berdasarkan umur (dalam cm)
     */
    private function estimateHeight($umurBulan)
    {
        // Tinggi lahir rata-rata 50 cm, bertambah sekitar 2.5 cm per bulan di 6 bulan pertama
        // kemudian 1.5 cm per bulan selanjutnya
        if ($umurBulan <= 6) {
            $tinggiEstimasi = 50 + ($umurBulan * 2.5);
        } else {
            $tinggiEstimasi = 50 + (6 * 2.5) + (($umurBulan - 6) * 1.5);
        }
        
        // Tambahkan variasi random ±10%
        $variasi = $tinggiEstimasi * (rand(-10, 10) / 100);
        
        return round($tinggiEstimasi + $variasi, 2);
    }
}
