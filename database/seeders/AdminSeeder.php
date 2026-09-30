<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public const EMAIL = 'admin@wikrama.test';

    /**
     * Kata sandi khusus pengembangan lokal. Ganti segera bila seeder ini
     * pernah dijalankan di server yang dapat diakses orang lain.
     */
    public const KATA_SANDI = 'password';

    /**
     * Akun Admin untuk masuk panel setelah `migrate:fresh --seed`.
     *
     * Tanpa akun ini database hasil seed tidak memiliki pengguna sama sekali,
     * sehingga panel tidak dapat dibuka dan JelantahSeeder mencatat setoran
     * tanpa pencatat. Akun yang sudah ada tidak diubah, termasuk kata sandinya.
     */
    public function run(): void
    {
        if (User::query()->where('email', self::EMAIL)->exists()) {
            return;
        }

        User::factory()->admin()->create([
            'name' => 'Admin Wikrama',
            'email' => self::EMAIL,
            'password' => self::KATA_SANDI,
        ]);
    }
}
