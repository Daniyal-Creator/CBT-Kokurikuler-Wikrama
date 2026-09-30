<?php

namespace Tests\Feature;

use App\Enums\PeranPengguna;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Resources\Pengguna\Pages\ListPengguna;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrasiTest extends TestCase
{
    use RefreshDatabase;

    public function test_halaman_daftar_dapat_dibuka_tanpa_login(): void
    {
        $this->get('/admin/register')->assertSuccessful();
    }

    public function test_pendaftar_menjadi_guru_yang_menunggu_persetujuan_dan_tidak_langsung_masuk(): void
    {
        Livewire::test(Register::class)
            ->fillForm([
                'name' => 'Bu Sari',
                'email' => 'sari@wikrama.test',
                'password' => 'rahasia-panjang',
                'passwordConfirmation' => 'rahasia-panjang',
            ])
            ->call('register')
            ->assertHasNoFormErrors()
            ->assertRedirect('/admin/login');

        $pendaftar = User::where('email', 'sari@wikrama.test')->sole();

        $this->assertSame(PeranPengguna::Guru, $pendaftar->peran);
        $this->assertFalse($pendaftar->sudahDisetujui());
        $this->assertGuest();
    }

    public function test_login_akun_menunggu_persetujuan_mendapat_pesan_yang_jelas(): void
    {
        User::factory()->menungguPersetujuan()->create([
            'email' => 'sari@wikrama.test',
            'password' => 'rahasia-panjang',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'sari@wikrama.test',
                'password' => 'rahasia-panjang',
            ])
            ->call('authenticate')
            ->assertHasFormErrors(['email'])
            ->assertSee('menunggu persetujuan Admin');

        $this->assertGuest();
    }

    public function test_login_akun_yang_sudah_disetujui_berhasil(): void
    {
        $guru = User::factory()->create([
            'email' => 'budi@wikrama.test',
            'password' => 'rahasia-panjang',
        ]);

        Livewire::test(Login::class)
            ->fillForm([
                'email' => 'budi@wikrama.test',
                'password' => 'rahasia-panjang',
            ])
            ->call('authenticate')
            ->assertHasNoFormErrors();

        $this->assertAuthenticatedAs($guru);
    }

    public function test_admin_menyetujui_pendaftar_dari_daftar_pengguna(): void
    {
        $admin = User::factory()->admin()->create();
        $pendaftar = User::factory()->menungguPersetujuan()->create();

        $this->actingAs($admin);

        Livewire::test(ListPengguna::class)
            ->callTableAction('setujui', $pendaftar);

        $this->assertTrue($pendaftar->fresh()->sudahDisetujui());
    }
}
