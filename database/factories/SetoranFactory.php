<?php

namespace Database\Factories;

use App\Enums\StatusCatatan;
use App\Models\Projek;
use App\Models\Setoran;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setoran>
 */
class SetoranFactory extends Factory
{
    public function definition(): array
    {
        return [
            'projek_id' => Projek::factory(),
            'siswa_id' => Siswa::factory(),
            'tanggal' => fake()->dateTimeBetween('-2 months')->format('Y-m-d'),
            'jumlah_liter' => fake()->randomFloat(2, 0.5, 5),
        ];
    }

    /**
     * Status diisi lewat forceFill agar tidak menembus `$guarded` pada model,
     * yang memang ada untuk memaksa perpindahan status lewat metode resmi.
     */
    public function terverifikasi(): static
    {
        return $this->afterCreating(function (Setoran $setoran): void {
            $setoran->forceFill([
                'status' => StatusCatatan::Terverifikasi,
                'diverifikasi_pada' => now(),
            ])->save();
        });
    }
}
