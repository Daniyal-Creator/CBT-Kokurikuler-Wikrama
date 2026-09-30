<?php

namespace Database\Factories;

use App\Models\Kelas;
use App\Models\PenempatanSiswa;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<PenempatanSiswa>
 */
class PenempatanSiswaFactory extends Factory
{
    public function definition(): array
    {
        // `tahun_ajaran_id` sengaja tidak diisi di sini — model yang menyalinnya
        // dari kelas, sehingga salinan itu tidak mungkin berbeda dari sumbernya.
        return [
            'siswa_id' => Siswa::factory(),
            'kelas_id' => Kelas::factory(),
        ];
    }
}
