<?php

namespace App\Filament\Pages\Auth;

use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Auth\Events\Registered;
use Filament\Auth\Http\Responses\Contracts\RegistrationResponse;
use Filament\Auth\Pages\Register as BaseRegister;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

/**
 * Pendaftaran terbuka untuk calon Guru, tetapi tidak langsung memberi akses.
 *
 * Guru dapat memverifikasi catatan — pintu yang terlalu penting untuk dibuka
 * bagi siapa pun yang kebetulan tahu alamat panel. Akun baru selalu berperan
 * Guru (nilai bawaan kolom) dan menunggu persetujuan Admin; formulir tidak
 * memuat kolom peran, sehingga tidak ada cara mendaftar sebagai Admin.
 */
class Register extends BaseRegister
{
    /**
     * Merek sudah tampil di panel hijau di samping formulir.
     */
    public function hasLogo(): bool
    {
        return false;
    }

    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        if ($this->isRegisterRateLimited($this->data['email'] ?? '')) {
            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        event(new Registered($user));

        // Sengaja tidak login: akun ini belum dapat masuk panel, dan membuat
        // sesi untuknya hanya berujung pada halaman 403 yang membingungkan.
        Notification::make()
            ->title('Pendaftaran terkirim')
            ->body('Akun Anda menunggu persetujuan Admin. Silakan masuk setelah disetujui.')
            ->success()
            ->persistent()
            ->send();

        $this->redirect(Filament::getLoginUrl());

        return null;
    }
}
