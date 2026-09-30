<?php

namespace Database\Factories;

use App\Models\Penjualan;
use App\Models\Projek;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Penjualan>
 */
class PenjualanFactory extends Factory
{
    public function definition(): array
    {
        $jumlahKg = fake()->randomFloat(2, 10, 60);
        $hargaPerKg = fake()->randomElement([4_000, 4_500, 5_000, 5_500]);

        return [
            'projek_id' => Projek::factory(),
            'tanggal' => fake()->dateTimeBetween('-2 months')->format('Y-m-d'),
            'pembeli' => 'Pengepul '.fake()->lastName(),
            'jumlah_kg' => $jumlahKg,
            'harga_per_kg' => $hargaPerKg,
            'total_rupiah' => (int) round($jumlahKg * $hargaPerKg, -2),
        ];
    }
}
