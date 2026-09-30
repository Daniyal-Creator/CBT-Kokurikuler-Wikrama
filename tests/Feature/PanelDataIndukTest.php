<?php

namespace Tests\Feature;

use App\Models\Kelas;
use App\Models\PenempatanSiswa;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PanelDataIndukTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->admin()->create());
    }

    /**
     * @return array<string, array{string}>
     */
    public static function halamanDataInduk(): array
    {
        return [
            'daftar tahun ajaran' => ['/admin/tahun-ajaran'],
            'daftar kelas' => ['/admin/kelas'],
            'daftar siswa' => ['/admin/siswa'],
        ];
    }

    #[DataProvider('halamanDataInduk')]
    public function test_halaman_data_induk_dapat_dibuka(string $halaman): void
    {
        $this->get($halaman)->assertSuccessful();
    }

    public function test_tamu_tidak_dapat_membuka_panel(): void
    {
        auth()->logout();

        $this->get('/admin/siswa')->assertRedirect('/admin/login');
    }

    /**
     * Filament hanya membuka pintu untuk semua orang ketika APP_ENV=local.
     * Tes ini berjalan di lingkungan "testing", sehingga ia membuktikan panel
     * tetap dapat dibuka di hosting — bukan hanya di laptop pengembang.
     */
    public function test_admin_dan_guru_sama_sama_dapat_membuka_panel(): void
    {
        $this->assertTrue(User::factory()->admin()->create()->canAccessPanel(
            Filament::getPanel('admin')
        ));

        $this->assertTrue(User::factory()->create()->canAccessPanel(
            Filament::getPanel('admin')
        ));
    }

    public function test_daftar_siswa_menampilkan_kelas_pada_tahun_ajaran_aktif(): void
    {
        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $tahunIni = TahunAjaran::factory()->aktif()->create(['nama' => '2025/2026']);

        $siswa = Siswa::factory()->create(['nama' => 'Rani Puspita']);

        PenempatanSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => Kelas::factory()->create([
                'tahun_ajaran_id' => $tahunLalu->id,
                'nama' => 'X RPL 1',
            ])->id,
        ]);

        PenempatanSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => Kelas::factory()->create([
                'tahun_ajaran_id' => $tahunIni->id,
                'nama' => 'XI RPL 1',
            ])->id,
        ]);

        $this->assertSame('XI RPL 1', $siswa->penempatanAktif->kelas->nama);
    }

    public function test_siswa_tanpa_penempatan_di_tahun_aktif_tidak_mengaku_punya_kelas(): void
    {
        TahunAjaran::factory()->aktif()->create(['nama' => '2025/2026']);

        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $siswa = Siswa::factory()->create();

        PenempatanSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => Kelas::factory()->create(['tahun_ajaran_id' => $tahunLalu->id])->id,
        ]);

        $this->assertNull($siswa->penempatanAktif);
    }
}
