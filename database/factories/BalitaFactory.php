<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Balita;
use App\Models\Posyandu;


/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Balita>
 */
class BalitaFactory extends Factory
{

    protected $model = balita::class;

    public function definition(): array
    {   $Angka_random = $this->faker->numberBetween(1, 5);
        $alamat = $Angka_random . '/' . $Angka_random + 1;
        return [
            'nama' => $this->faker->firstname(),
            'nik' => $this->faker->unique()->numerify('################'),
            'nama_ibu' => $this->faker->firstname(),
            'tanggal_lahir' => $this->faker->dateTimeBetween('-6 years', 'now')->format('Y-m-d'),
            'alamat' => $alamat,
            'jenis_kelamin' => $this->faker->randomElement(['L', 'P']),
            'posyandu_id' => null,
            'Buku_KIA' => $this->faker->boolean(90) ? 'ada' : 'tidak_ada',
        ];
    }
}
