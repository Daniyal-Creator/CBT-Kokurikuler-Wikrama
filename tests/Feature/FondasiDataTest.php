<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\PenempatanSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FondasiDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_penempatan_menyalin_tahun_ajaran_dari_kelasnya(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $kelas = Kelas::factory()->create(['tahun_ajaran_id' => $tahunAjaran->id]);

        $penempatan = PenempatanSiswa::create([
            'siswa_id' => Siswa::factory()->create()->id,
            'kelas_id' => $kelas->id,
        ]);

        $this->assertSame($tahunAjaran->id, $penempatan->tahun_ajaran_id);
    }

    public function test_siswa_tidak_dapat_menempati_dua_kelas_dalam_satu_tahun_ajaran(): void
    {
        $tahunAjaran = TahunAjaran::factory()->create();
        $siswa = Siswa::factory()->create();

        foreach (['XI RPL 1', 'XI RPL 2'] as $nama) {
            $kelas = Kelas::factory()->create([
                'tahun_ajaran_id' => $tahunAjaran->id,
                'nama' => $nama,
            ]);

            if ($nama === 'XI RPL 2') {
                $this->expectException(QueryException::class);
            }

            PenempatanSiswa::create([
                'siswa_id' => $siswa->id,
                'kelas_id' => $kelas->id,
            ]);
        }
    }

    public function test_siswa_yang_naik_kelas_tetap_tercatat_di_kelas_tahun_sebelumnya(): void
    {
        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $tahunIni = TahunAjaran::factory()->create(['nama' => '2025/2026']);

        $kelasLama = Kelas::factory()->create([
            'tahun_ajaran_id' => $tahunLalu->id,
            'nama' => 'X RPL 1',
        ]);

        $kelasBaru = Kelas::factory()->create([
            'tahun_ajaran_id' => $tahunIni->id,
            'nama' => 'XI RPL 1',
        ]);

        $siswa = Siswa::factory()->create();

        PenempatanSiswa::create(['siswa_id' => $siswa->id, 'kelas_id' => $kelasLama->id]);
        PenempatanSiswa::create(['siswa_id' => $siswa->id, 'kelas_id' => $kelasBaru->id]);

        $this->assertSame('X RPL 1', $siswa->kelasPada($tahunLalu)->nama);
        $this->assertSame('XI RPL 1', $siswa->kelasPada($tahunIni)->nama);
    }

    public function test_kelas_yang_sudah_lewat_tidak_kehilangan_muridnya_saat_tahun_baru_dibuka(): void
    {
        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $kelasLama = Kelas::factory()->create(['tahun_ajaran_id' => $tahunLalu->id]);

        $siswa = Siswa::factory()->count(3)->create();

        foreach ($siswa as $satuSiswa) {
            PenempatanSiswa::create(['siswa_id' => $satuSiswa->id, 'kelas_id' => $kelasLama->id]);
        }

        TahunAjaran::factory()->create(['nama' => '2025/2026'])->jadikanAktif();

        $this->assertCount(3, $kelasLama->fresh()->siswa);
    }

    public function test_nama_kelas_unik_dalam_satu_tahun_ajaran_tetapi_boleh_berulang_di_tahun_lain(): void
    {
        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $tahunIni = TahunAjaran::factory()->create(['nama' => '2025/2026']);

        Kelas::factory()->create(['tahun_ajaran_id' => $tahunLalu->id, 'nama' => 'X RPL 1']);
        Kelas::factory()->create(['tahun_ajaran_id' => $tahunIni->id, 'nama' => 'X RPL 1']);

        $this->assertSame(2, Kelas::where('nama', 'X RPL 1')->count());

        $this->expectException(QueryException::class);

        Kelas::factory()->create(['tahun_ajaran_id' => $tahunIni->id, 'nama' => 'X RPL 1']);
    }

    public function test_hanya_satu_tahun_ajaran_yang_aktif(): void
    {
        $tahunLalu = TahunAjaran::factory()->aktif()->create(['nama' => '2024/2025']);
        $tahunIni = TahunAjaran::factory()->create(['nama' => '2025/2026']);

        $tahunIni->jadikanAktif();

        $this->assertTrue($tahunIni->fresh()->aktif);
        $this->assertFalse($tahunLalu->fresh()->aktif);
        $this->assertSame(1, TahunAjaran::aktif()->count());
    }

    public function test_nis_tidak_boleh_kembar(): void
    {
        Siswa::factory()->create(['nis' => '2025001']);

        $this->expectException(QueryException::class);

        Siswa::factory()->create(['nis' => '2025001']);
    }
}
