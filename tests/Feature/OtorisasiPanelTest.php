<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class OtorisasiPanelTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function halamanKhususAdmin(): array
    {
        return [
            'tahun ajaran' => ['/admin/tahun-ajaran'],
            'kelas' => ['/admin/kelas'],
            'siswa' => ['/admin/siswa'],
            'projek' => ['/admin/projek'],
            'pengguna' => ['/admin/pengguna'],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function halamanKegiatan(): array
    {
        return [
            'setoran' => ['/admin/setoran'],
            'penjualan' => ['/admin/penjualan'],
        ];
    }

    #[DataProvider('halamanKhususAdmin')]
    public function test_admin_dapat_membuka_data_induk(string $halaman): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get($halaman)->assertSuccessful();
    }

    #[DataProvider('halamanKhususAdmin')]
    public function test_guru_ditolak_dari_data_induk(string $halaman): void
    {
        $this->actingAs(User::factory()->create());

        $this->get($halaman)->assertForbidden();
    }

    #[DataProvider('halamanKegiatan')]
    public function test_guru_dapat_membuka_halaman_kegiatan(string $halaman): void
    {
        $this->actingAs(User::factory()->create());

        $this->get($halaman)->assertSuccessful();
    }

    public function test_akun_yang_belum_disetujui_tidak_dapat_masuk_panel(): void
    {
        $pendaftar = User::factory()->menungguPersetujuan()->create();

        $this->assertFalse($pendaftar->canAccessPanel(Filament::getPanel('admin')));

        $this->actingAs($pendaftar)->get('/admin/setoran')->assertForbidden();
    }

    public function test_akun_dapat_masuk_panel_setelah_disetujui_admin(): void
    {
        $pendaftar = User::factory()->menungguPersetujuan()->create();

        $pendaftar->setujui();

        $this->assertTrue($pendaftar->fresh()->canAccessPanel(Filament::getPanel('admin')));
    }

    public function test_middleware_peran_menolak_peran_yang_tidak_disebut(): void
    {
        Route::middleware(['web', 'auth', 'peran:admin'])->get('/_uji-peran', fn () => 'ok');

        $this->actingAs(User::factory()->create())->get('/_uji-peran')->assertForbidden();
        $this->actingAs(User::factory()->admin()->create())->get('/_uji-peran')->assertOk();
    }

    public function test_middleware_peran_menolak_akun_yang_belum_disetujui(): void
    {
        Route::middleware(['web', 'auth', 'peran:admin,guru'])->get('/_uji-peran', fn () => 'ok');

        $this->actingAs(User::factory()->admin()->menungguPersetujuan()->create())
            ->get('/_uji-peran')
            ->assertForbidden();
    }
}
