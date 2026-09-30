<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Trait `WithoutModelEvents` sengaja tidak dipakai.
     *
     * PenempatanSiswa menyalin `tahun_ajaran_id` dari kelasnya lewat event
     * `saving`. Mematikan event model akan membuat salinan itu kosong dan
     * seeding gagal — kegagalan yang benar, tetapi lebih baik tidak diundang.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,
            FondasiSeeder::class,
            JelantahSeeder::class,
        ]);
    }
}
