<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class kunjungan_balitaFactory extends Factory
{
    
    public function definition(): array
    {
        $berat_badan = $this->faker->numberBetween(7, 10);
        $tinggi_badan = $this->faker->numberBetween(80, 97);
        return [
            'balita_id' => null,
            'tanggal_kunjungan' => null,
            'berat_badan' => $berat_badan,
            'tinggi_badan' => $tinggi_badan,
            'Status_gizi' => $this->faker->randomElement(['N', 'K', 'T']),
            'rambu_gizi'=> $this->faker->randomElement(['O', 'N1', 'N2', 'T1', 'T2', 'T3']),
        ];
    }
}
