<?php

namespace Database\Factories;

use App\Models\Projek;
use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Projek>
 */
class ProjekFactory extends Factory
{
    public function definition(): array
    {
        return [
            'tahun_ajaran_id' => TahunAjaran::factory(),
            'nama' => 'Gaya Hidup Berkelanjutan',
            'tanggal_mulai' => '2025-07-14',
            'tanggal_selesai' => '2025-12-19',
            'hari_pengumpulan' => 3,
            'faktor_konversi_liter_ke_kg' => 0.90,
            'aktif' => false,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => ['aktif' => true]);
    }
}
