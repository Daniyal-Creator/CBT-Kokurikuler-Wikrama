<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\PenempatanSiswa;
use App\Models\Penjualan;
use App\Models\Projek;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BerandaTest extends TestCase
{
    use RefreshDatabase;

    private Projek $projek;

    private TahunAjaran $tahunAjaran;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tahunAjaran = TahunAjaran::factory()->aktif()->create();
        $this->projek = Projek::factory()->aktif()->create(['tahun_ajaran_id' => $this->tahunAjaran->id]);
    }

    private function muridDi(Kelas $kelas, string $nama = 'Murid'): Siswa
    {
        $siswa = Siswa::factory()->create(['nama' => $nama]);

        PenempatanSiswa::create(['siswa_id' => $siswa->id, 'kelas_id' => $kelas->id]);

        return $siswa;
    }

    private function setor(Siswa $siswa, float $liter): Setoran
    {
        return Setoran::factory()->terverifikasi()->create([
            'projek_id' => $this->projek->id,
            'siswa_id' => $siswa->id,
            'jumlah_liter' => $liter,
        ]);
    }

    public function test_papan_peringkat_mengurutkan_kelas_menurut_total_liter_terverifikasi(): void
    {
        $kelasA = Kelas::factory()->create(['tahun_ajaran_id' => $this->tahunAjaran->id, 'nama' => 'X RPL 1']);
        $kelasB = Kelas::factory()->create(['tahun_ajaran_id' => $this->tahunAjaran->id, 'nama' => 'X RPL 2']);
        Kelas::factory()->create(['tahun_ajaran_id' => $this->tahunAjaran->id, 'nama' => 'X RPL 3']);

        $this->setor($this->muridDi($kelasA), 3);
        $this->setor($this->muridDi($kelasB), 5);
        $this->setor($this->muridDi($kelasB), 1);

        // Belum terverifikasi — tidak boleh ikut dihitung.
        Setoran::factory()->create([
            'projek_id' => $this->projek->id,
            'siswa_id' => $this->muridDi($kelasA)->id,
            'jumlah_liter' => 50,
        ]);

        $peringkat = $this->projek->papanPeringkatKelas();

        $this->assertSame(['X RPL 2', 'X RPL 1', 'X RPL 3'], $peringkat->pluck('kelas')->all());
        $this->assertSame(6.0, $peringkat[0]['total_liter']);
        $this->assertSame(3.0, $peringkat[0]['rata_rata_liter']);
        $this->assertSame(1.5, $peringkat[1]['rata_rata_liter']);
        $this->assertSame(0.0, $peringkat[2]['total_liter']);
    }

    public function test_papan_peringkat_memakai_kelas_pada_tahun_ajaran_projek(): void
    {
        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $kelasLama = Kelas::factory()->create(['tahun_ajaran_id' => $tahunLalu->id, 'nama' => 'X RPL 1']);
        $kelasBaru = Kelas::factory()->create(['tahun_ajaran_id' => $this->tahunAjaran->id, 'nama' => 'XI RPL 1']);

        $siswa = $this->muridDi($kelasLama);
        PenempatanSiswa::create(['siswa_id' => $siswa->id, 'kelas_id' => $kelasBaru->id]);

        $this->setor($siswa, 4);

        $peringkat = $this->projek->papanPeringkatKelas();

        $this->assertSame(['XI RPL 1'], $peringkat->pluck('kelas')->all());
        $this->assertSame(4.0, $peringkat[0]['total_liter']);
    }

    public function test_beranda_terbuka_tanpa_login_dan_menampilkan_agregat(): void
    {
        $kelas = Kelas::factory()->create(['tahun_ajaran_id' => $this->tahunAjaran->id, 'nama' => 'XII TKJ 2']);
        $this->setor($this->muridDi($kelas, 'Rani Puspita'), 12.5);

        Penjualan::factory()->create([
            'projek_id' => $this->projek->id,
            'pembeli' => 'Pengepul Rahasia',
            'harga_per_kg' => 5_250,
            'total_rupiah' => 56_000,
        ]);

        $this->get('/')
            ->assertSuccessful()
            ->assertSee('XII TKJ 2')
            ->assertSee('12,50')
            ->assertSee('Rp 56.000')
            ->assertDontSee('Rani Puspita')
            ->assertDontSee('Pengepul Rahasia')
            ->assertDontSee('5.250');
    }

    public function test_beranda_tetap_terbuka_tanpa_projek_aktif(): void
    {
        Projek::query()->update(['aktif' => false]);

        $this->get('/')
            ->assertSuccessful()
            ->assertSee('Belum ada projek yang berjalan');
    }
}
