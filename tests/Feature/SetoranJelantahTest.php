<?php

namespace Tests\Feature;

use App\Enums\StatusCatatan;
use App\Models\Kelas;
use App\Models\PenempatanSiswa;
use App\Models\Projek;
use App\Models\Setoran;
use App\Models\Siswa;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SetoranJelantahTest extends TestCase
{
    use RefreshDatabase;

    private function projek(): Projek
    {
        return Projek::factory()->aktif()->create([
            'tahun_ajaran_id' => TahunAjaran::factory()->aktif()->create()->id,
        ]);
    }

    public function test_setoran_baru_menunggu_verifikasi(): void
    {
        $setoran = Setoran::factory()->create(['projek_id' => $this->projek()->id]);

        $this->assertSame(StatusCatatan::Diajukan, $setoran->status);
        $this->assertNull($setoran->diverifikasi_pada);
    }

    public function test_status_tidak_dapat_diisi_lewat_pengisian_massal(): void
    {
        $setoran = Setoran::create([
            'projek_id' => $this->projek()->id,
            'siswa_id' => Siswa::factory()->create()->id,
            'tanggal' => '2025-09-17',
            'jumlah_liter' => 2.5,
            'status' => StatusCatatan::Terverifikasi->value,
        ]);

        $this->assertSame(StatusCatatan::Diajukan, $setoran->fresh()->status);
    }

    public function test_verifikasi_mencatat_guru_dan_waktunya(): void
    {
        $guru = User::factory()->create();
        $setoran = Setoran::factory()->create(['projek_id' => $this->projek()->id]);

        $setoran->verifikasi($guru);

        $setoran->refresh();

        $this->assertSame(StatusCatatan::Terverifikasi, $setoran->status);
        $this->assertSame($guru->id, $setoran->diverifikasi_oleh_user_id);
        $this->assertNotNull($setoran->diverifikasi_pada);
    }

    public function test_hanya_setoran_terverifikasi_yang_masuk_rekap(): void
    {
        $projek = $this->projek();
        $guru = User::factory()->create();

        Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 3]);
        Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 4])->verifikasi($guru);
        Setoran::factory()->create(['projek_id' => $projek->id, 'jumlah_liter' => 5])->tolak($guru, 'Botol kosong');

        $this->assertSame(4.0, (float) Setoran::terverifikasi()->sum('jumlah_liter'));
        $this->assertSame(1, Setoran::menungguVerifikasi()->count());
    }

    public function test_koreksi_atas_setoran_terverifikasi_meninggalkan_jejak(): void
    {
        $guru = User::factory()->create();
        $setoran = Setoran::factory()->create([
            'projek_id' => $this->projek()->id,
            'jumlah_liter' => 2.00,
        ]);

        $setoran->verifikasi($guru);

        $this->actingAs($guru);
        $setoran->update(['jumlah_liter' => 3.50]);

        $jejak = $setoran->jejakPerubahan()->where('kolom', 'jumlah_liter')->sole();

        $this->assertSame('2.00', $jejak->nilai_lama);
        $this->assertSame('3.50', $jejak->nilai_baru);
        $this->assertSame($guru->id, $jejak->user_id);
    }

    public function test_koreksi_sebelum_verifikasi_tidak_meninggalkan_jejak(): void
    {
        $setoran = Setoran::factory()->create([
            'projek_id' => $this->projek()->id,
            'jumlah_liter' => 2.00,
        ]);

        $setoran->update(['jumlah_liter' => 2.75]);

        $this->assertSame(0, $setoran->jejakPerubahan()->count());
    }

    public function test_pembatalan_verifikasi_tercatat_dan_mengosongkan_verifikator(): void
    {
        $guru = User::factory()->create();
        $setoran = Setoran::factory()->create(['projek_id' => $this->projek()->id]);

        $setoran->verifikasi($guru);

        $this->actingAs($guru);
        $setoran->batalkanVerifikasi($guru);

        $setoran->refresh();

        $this->assertSame(StatusCatatan::Diajukan, $setoran->status);
        $this->assertNull($setoran->diverifikasi_oleh_user_id);
        $this->assertSame(
            'terverifikasi',
            $setoran->jejakPerubahan()->where('kolom', 'status')->sole()->nilai_lama
        );
    }

    public function test_seorang_murid_boleh_menyetor_lebih_dari_sekali_pada_hari_yang_sama(): void
    {
        $projek = $this->projek();
        $siswa = Siswa::factory()->create();

        foreach ([1.5, 2.0] as $liter) {
            Setoran::factory()->create([
                'projek_id' => $projek->id,
                'siswa_id' => $siswa->id,
                'tanggal' => '2025-09-17',
                'jumlah_liter' => $liter,
            ]);
        }

        $this->assertSame(2, Setoran::where('siswa_id', $siswa->id)->count());
    }

    public function test_kelas_penyetor_diturunkan_dari_penempatan_pada_tahun_ajaran_projek(): void
    {
        $tahunLalu = TahunAjaran::factory()->create(['nama' => '2024/2025']);
        $tahunIni = TahunAjaran::factory()->aktif()->create(['nama' => '2025/2026']);

        $projek = Projek::factory()->aktif()->create(['tahun_ajaran_id' => $tahunIni->id]);
        $siswa = Siswa::factory()->create();

        PenempatanSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => Kelas::factory()->create(['tahun_ajaran_id' => $tahunLalu->id, 'nama' => 'X RPL 1'])->id,
        ]);

        PenempatanSiswa::create([
            'siswa_id' => $siswa->id,
            'kelas_id' => Kelas::factory()->create(['tahun_ajaran_id' => $tahunIni->id, 'nama' => 'XI RPL 1'])->id,
        ]);

        $setoran = Setoran::factory()->create(['projek_id' => $projek->id, 'siswa_id' => $siswa->id]);

        $this->assertSame('XI RPL 1', $setoran->kelas()->nama);
    }

    public function test_setoran_murid_tanpa_penempatan_tidak_mengaku_punya_kelas(): void
    {
        $setoran = Setoran::factory()->create(['projek_id' => $this->projek()->id]);

        $this->assertNull($setoran->kelas());
    }

    public function test_liter_dikonversi_ke_kilogram_memakai_faktor_projek(): void
    {
        $projek = Projek::factory()->create(['faktor_konversi_liter_ke_kg' => 0.92]);

        $this->assertSame(9.2, $projek->literKeKilogram(10));
    }

    public function test_hanya_satu_projek_yang_aktif(): void
    {
        $lama = Projek::factory()->aktif()->create(['nama' => 'Projek Lama']);
        $baru = Projek::factory()->create(['nama' => 'Projek Baru']);

        $baru->jadikanAktif();

        $this->assertFalse($lama->fresh()->aktif);
        $this->assertSame(1, Projek::aktif()->count());
    }

    public function test_halaman_projek_dan_setoran_dapat_dibuka(): void
    {
        $this->actingAs(User::factory()->admin()->create());

        $this->get('/admin/projek')->assertSuccessful();
        $this->get('/admin/setoran')->assertSuccessful();
    }
}
