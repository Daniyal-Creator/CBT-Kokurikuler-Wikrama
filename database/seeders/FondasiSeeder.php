<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\PenempatanSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\Seeder;

class FondasiSeeder extends Seeder
{
    /**
     * Data fondasi yang cukup untuk mencoba alur sungguhan:
     * dua tahun ajaran berurutan, dengan sebagian murid yang sama naik kelas.
     *
     * Kenaikan kelas itu bukan hiasan — ia membuktikan bahwa rekap tahun lalu
     * tetap utuh setelah murid berpindah kelas, yang merupakan alasan
     * PenempatanSiswa ada.
     */
    public function run(): void
    {
        $tahunLalu = TahunAjaran::factory()->create([
            'nama' => '2024/2025',
            'tanggal_mulai' => '2024-07-15',
            'tanggal_selesai' => '2025-06-30',
        ]);

        $tahunIni = TahunAjaran::factory()->create([
            'nama' => '2025/2026',
            'tanggal_mulai' => '2025-07-14',
            'tanggal_selesai' => '2026-06-30',
        ]);

        $tahunIni->jadikanAktif();

        $jurusan = ['RPL 1', 'RPL 2', 'TKJ 1', 'DKV 1'];

        foreach ([$tahunLalu, $tahunIni] as $tahunAjaran) {
            foreach (['X', 'XI', 'XII'] as $tingkat) {
                foreach ($jurusan as $rombel) {
                    Kelas::factory()->create([
                        'tahun_ajaran_id' => $tahunAjaran->id,
                        'nama' => $tingkat.' '.$rombel,
                        'tingkat' => $tingkat,
                    ]);
                }
            }
        }

        $kelasTahunLalu = Kelas::where('tahun_ajaran_id', $tahunLalu->id)->get();
        $kelasTahunIni = Kelas::where('tahun_ajaran_id', $tahunIni->id)->get();

        $naikKelas = ['X' => 'XI', 'XI' => 'XII'];

        foreach ($kelasTahunLalu as $kelasLama) {
            $siswaKelasIni = Siswa::factory()->count(30)->create();

            foreach ($siswaKelasIni as $siswa) {
                PenempatanSiswa::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelasLama->id,
                ]);
            }

            // Murid kelas XII lulus dan tidak menempati kelas mana pun tahun ini.
            if (! isset($naikKelas[$kelasLama->tingkat])) {
                continue;
            }

            $namaKelasBaru = str_replace(
                $kelasLama->tingkat.' ',
                $naikKelas[$kelasLama->tingkat].' ',
                $kelasLama->nama
            );

            $kelasBaru = $kelasTahunIni->firstWhere('nama', $namaKelasBaru);

            foreach ($siswaKelasIni as $siswa) {
                PenempatanSiswa::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelasBaru->id,
                ]);
            }
        }

        // Kelas X tahun ini diisi murid baru.
        foreach ($kelasTahunIni->where('tingkat', 'X') as $kelasBaru) {
            foreach (Siswa::factory()->count(30)->create() as $siswa) {
                PenempatanSiswa::create([
                    'siswa_id' => $siswa->id,
                    'kelas_id' => $kelasBaru->id,
                ]);
            }
        }
    }
}
