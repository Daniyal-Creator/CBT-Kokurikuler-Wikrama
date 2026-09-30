<?php

namespace Tests\Feature;

use App\Filament\Resources\Penjualans\Pages\CreatePenjualan;
use App\Filament\Widgets\StokJelantahWidget;
use App\Models\Penjualan;
use App\Models\Projek;
use App\Models\Setoran;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PenjualanJelantahTest extends TestCase
{
    use RefreshDatabase;

    private function projek(float $faktor = 0.90): Projek
    {
        return Projek::factory()->aktif()->create([
            'tahun_ajaran_id' => TahunAjaran::factory()->aktif()->create()->id,
            'faktor_konversi_liter_ke_kg' => $faktor,
        ]);
    }

    public function test_stok_hanya_menghitung_setoran_terverifikasi(): void
    {
        $projek = $this->projek();
        $guru = User::factory()->create();

        Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 10])->verifikasi($guru);
        Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 5]);
        Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 7])->tolak($guru);

        $this->assertSame(9.0, $projek->stokJelantahKilogram());
    }

    public function test_stok_bertambah_setelah_verifikasi_dan_berkurang_setelah_penjualan(): void
    {
        $projek = $this->projek();
        $guru = User::factory()->create();

        $setoran = Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 20]);
        $this->assertSame(0.0, $projek->stokJelantahKilogram());

        $setoran->verifikasi($guru);
        $this->assertSame(18.0, $projek->stokJelantahKilogram());

        Penjualan::factory()->create(['projek_id' => $projek->id, 'jumlah_kg' => 12.5]);
        $this->assertSame(5.5, $projek->stokJelantahKilogram());
    }

    public function test_stok_memakai_faktor_konversi_projek(): void
    {
        $projek = $this->projek(faktor: 0.92);

        Setoran::factory()->terverifikasi()->create(['projek_id' => $projek->id, 'jumlah_liter' => 10]);

        $this->assertSame(9.2, $projek->stokJelantahKilogram());
    }

    /**
     * Stok negatif adalah tanda salah input, bukan keadaan yang boleh dibulatkan
     * ke nol — menyembunyikannya membuat kesalahan itu tidak pernah ditemukan.
     */
    public function test_stok_negatif_tidak_disembunyikan(): void
    {
        $projek = $this->projek();

        Setoran::factory()->terverifikasi()->create(['projek_id' => $projek->id, 'jumlah_liter' => 10]);
        Penjualan::factory()->create(['projek_id' => $projek->id, 'jumlah_kg' => 15]);

        $this->assertSame(-6.0, $projek->stokJelantahKilogram());
    }

    public function test_penjualan_projek_lain_tidak_mengurangi_stok(): void
    {
        $projek = $this->projek();
        $projekLain = Projek::factory()->create();

        Setoran::factory()->terverifikasi()->create(['projek_id' => $projek->id, 'jumlah_liter' => 10]);
        Penjualan::factory()->create(['projek_id' => $projekLain->id, 'jumlah_kg' => 5]);

        $this->assertSame(9.0, $projek->stokJelantahKilogram());
    }

    public function test_total_rupiah_penjualan_dijumlahkan_per_projek(): void
    {
        $projek = $this->projek();

        Penjualan::factory()->create(['projek_id' => $projek->id, 'total_rupiah' => 45_000]);
        Penjualan::factory()->create(['projek_id' => $projek->id, 'total_rupiah' => 30_500]);
        Penjualan::factory()->create(['total_rupiah' => 99_000]);

        $this->assertSame(75_500, $projek->totalPenjualanRupiah());
    }

    public function test_halaman_penjualan_dan_dasbor_dapat_dibuka(): void
    {
        $projek = $this->projek();
        Setoran::factory()->terverifikasi()->create(['projek_id' => $projek->id]);
        Penjualan::factory()->create(['projek_id' => $projek->id]);

        $this->actingAs(User::factory()->create());

        $this->get('/admin')->assertSuccessful();
        $this->get('/admin/penjualan')->assertSuccessful();
        $this->get('/admin/penjualan/create')->assertSuccessful();
    }

    public function test_widget_stok_memperingatkan_stok_negatif(): void
    {
        $projek = $this->projek();
        Setoran::factory()->terverifikasi()->create(['projek_id' => $projek->id, 'jumlah_liter' => 10]);
        Penjualan::factory()->create(['projek_id' => $projek->id, 'jumlah_kg' => 15]);

        $this->actingAs(User::factory()->create());

        Livewire::test(StokJelantahWidget::class)
            ->assertSee('-6,00 kg')
            ->assertSee('Stok negatif');
    }

    public function test_penjualan_dari_panel_mencatat_siapa_yang_menginput(): void
    {
        $projek = $this->projek();
        $guru = User::factory()->create();

        $this->actingAs($guru);

        Livewire::test(CreatePenjualan::class)
            ->fillForm([
                'projek_id' => $projek->id,
                'tanggal' => '2025-10-01',
                'pembeli' => 'Pengepul Sejahtera',
                'jumlah_kg' => 25,
                'harga_per_kg' => 5_000,
                'total_rupiah' => 125_000,
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $penjualan = Penjualan::sole();

        $this->assertSame($guru->id, $penjualan->dicatat_oleh_user_id);
        $this->assertSame(125_000, $penjualan->total_rupiah);
    }
}
