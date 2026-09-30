<?php

namespace Database\Seeders;

use App\Models\Penjualan;
use App\Models\Projek;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class JelantahSeeder extends Seeder
{
    /**
     * Setoran contoh pada beberapa hari Rabu berturut-turut.
     *
     * Sebagian sengaja dibiarkan menunggu verifikasi agar antrean guru tidak
     * kosong saat panel pertama kali dibuka — halaman verifikasi yang selalu
     * kosong tidak membuktikan apa pun.
     */
    public function run(): void
    {
        $tahunAjaran = TahunAjaran::aktif()->first();

        if ($tahunAjaran === null) {
            return;
        }

        $projek = Projek::factory()->create([
            'tahun_ajaran_id' => $tahunAjaran->id,
            'nama' => 'Gaya Hidup Berkelanjutan',
            'tanggal_mulai' => $tahunAjaran->tanggal_mulai,
            'tanggal_selesai' => $tahunAjaran->tanggal_selesai,
            'hari_pengumpulan' => 3,
        ]);

        $projek->jadikanAktif();

        $guru = User::query()->first();

        $penyetor = Siswa::query()
            ->whereHas('penempatanAktif')
            ->inRandomOrder()
            ->limit(120)
            ->get();

        $rabu = Carbon::today()->previous(Carbon::WEDNESDAY);

        foreach (range(0, 3) as $pekanLalu) {
            $tanggal = $rabu->copy()->subWeeks($pekanLalu);

            foreach ($penyetor->random(40) as $siswa) {
                $setoran = Setoran::create([
                    'projek_id' => $projek->id,
                    'siswa_id' => $siswa->id,
                    'tanggal' => $tanggal->toDateString(),
                    'jumlah_liter' => fake()->randomFloat(2, 0.5, 4),
                    'dicatat_oleh_user_id' => $guru?->id,
                ]);

                // Pekan terakhir dibiarkan menunggu verifikasi.
                if ($pekanLalu > 0 && $guru !== null) {
                    $setoran->verifikasi($guru);
                }
            }
        }

        // Satu penjualan yang tidak menghabiskan stok, agar angka stok di
        // dasbor terlihat berkurang tanpa menjadi nol.
        Penjualan::factory()->create([
            'projek_id' => $projek->id,
            'tanggal' => $rabu->copy()->subWeek()->addDays(2)->toDateString(),
            'jumlah_kg' => round($projek->stokJelantahKilogram() * 0.6, 2),
            'harga_per_kg' => 5_000,
            'total_rupiah' => (int) round($projek->stokJelantahKilogram() * 0.6 * 5_000, -2),
            'dicatat_oleh_user_id' => $guru?->id,
        ]);
    }
}
