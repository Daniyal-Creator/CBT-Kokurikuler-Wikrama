<?php

namespace Database\Factories;

use App\Models\TahunAjaran;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TahunAjaran>
 */
class TahunAjaranFactory extends Factory
{
    public function definition(): array
    {
        // unique() agar dua tahun ajaran yang dibuat dalam satu tes tidak
        // bertabrakan pada batasan unik nama.
        $tahunMulai = fake()->unique()->numberBetween(2000, 2100);

        return [
            'nama' => $tahunMulai.'/'.($tahunMulai + 1),
            'tanggal_mulai' => $tahunMulai.'-07-15',
            'tanggal_selesai' => ($tahunMulai + 1).'-06-30',
            'aktif' => false,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn (array $attributes) => ['aktif' => true]);
    }
}
