<?php

namespace Tests\Feature;

use App\Enums\StatusCatatan;
use App\Models\Projek;
use App\Models\Setoran;
use App\Models\TahunAjaran;
use App\Models\User;
use Database\Seeders\AdminSeeder;
use Database\Seeders\FondasiSeeder;
use Database\Seeders\JelantahSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_menghasilkan_data_yang_utuh(): void
    {
        User::factory()->admin()->create();

        $this->seed([FondasiSeeder::class, JelantahSeeder::class]);

        $this->assertSame(2, TahunAjaran::count());
        $this->assertSame(1, TahunAjaran::aktif()->count());
        $this->assertSame(1, Projek::aktif()->count());

        // Antrean verifikasi tidak boleh kosong: halaman verifikasi yang selalu
        // kosong tidak membuktikan alur itu bekerja.
        $this->assertGreaterThan(0, Setoran::menungguVerifikasi()->count());
        $this->assertGreaterThan(0, Setoran::terverifikasi()->count());

        // Setiap setoran harus dapat menyebut kelas penyetornya, karena seeder
        // hanya memilih siswa yang punya penempatan pada tahun ajaran aktif.
        $setoran = Setoran::with(['siswa.penempatan.kelas', 'projek'])->first();

        $this->assertNotNull($setoran->kelas());
        $this->assertContains($setoran->status, [StatusCatatan::Diajukan, StatusCatatan::Terverifikasi]);
    }

    public function test_seeder_admin_membuat_akun_yang_dapat_masuk_panel(): void
    {
        $this->seed(AdminSeeder::class);

        $admin = User::query()->where('email', AdminSeeder::EMAIL)->sole();

        $this->assertTrue($admin->adalahAdmin());
        $this->assertTrue($admin->sudahDisetujui());
        $this->assertTrue(Hash::check(AdminSeeder::KATA_SANDI, $admin->password));
    }

    public function test_seeder_admin_tidak_mengubah_akun_yang_sudah_ada(): void
    {
        User::factory()->admin()->create([
            'email' => AdminSeeder::EMAIL,
            'password' => 'kata-sandi-milik-sekolah',
        ]);

        $this->seed(AdminSeeder::class);

        $admin = User::query()->where('email', AdminSeeder::EMAIL)->sole();

        $this->assertTrue(Hash::check('kata-sandi-milik-sekolah', $admin->password));
    }
}
