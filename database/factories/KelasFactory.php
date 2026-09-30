<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Kelas>
 */
class KelasFactory extends Factory
{
    public function definition(): array
    {
        $tingkat = fake()->randomElement(['X', 'XI', 'XII']);

        return [
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'nama' => $tingkat.' '.fake()->randomElement(['RPL', 'TKJ', 'DKV']).' '.fake()->numberBetween(1, 3),
            'tingkat' => $tingkat,
        ];
    }
}
