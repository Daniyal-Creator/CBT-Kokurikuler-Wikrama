<?php

namespace App\Filament\Pages\Auth;

use App\Models\User;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{
    /**
     * Pendaftar yang belum disetujui mendapat pesan yang menjelaskan keadaannya,
     * bukan "email atau kata sandi salah" — pesan itu akan membuatnya mendaftar
     * ulang atau mengira kata sandinya keliru.
     *
     * Pemeriksaan dilakukan sesudah login bawaan gagal, agar pembatasan
     * percobaan tetap berlaku; dan pesannya hanya muncul bila kata sandi benar,
     * sehingga status sebuah akun tidak dapat diintip dengan menebak email.
     */
    public function authenticate(): ?LoginResponse
    {
        try {
            return parent::authenticate();
        } catch (ValidationException $gagal) {
            if ($this->adalahPendaftarYangMenunggu()) {
                throw ValidationException::withMessages([
                    'data.email' => 'Akun Anda masih menunggu persetujuan Admin.',
                ]);
            }

            throw $gagal;
        }
    }

    /**
     * Merek sudah tampil di panel hijau di samping formulir.
     */
    public function hasLogo(): bool
    {
        return false;
    }

    private function adalahPendaftarYangMenunggu(): bool
    {
        $user = User::query()->where('email', $this->data['email'] ?? null)->first();

        return $user !== null
            && ! $user->sudahDisetujui()
            && Hash::check((string) ($this->data['password'] ?? ''), $user->password);
    }
}
