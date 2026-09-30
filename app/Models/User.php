<?php

namespace App\Models;

use App\Enums\PeranPengguna;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * `disetujui_pada` sengaja tidak dapat diisi massal: persetujuan hanya boleh
 * terjadi lewat setujui(), agar formulir pendaftaran tidak dapat menyetujui
 * dirinya sendiri.
 */
#[Fillable(['name', 'email', 'password', 'peran'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'peran' => PeranPengguna::class,
            'disetujui_pada' => 'datetime',
        ];
    }

    /**
     * Tanpa metode ini Filament hanya mengizinkan siapa pun saat APP_ENV=local,
     * dan menolak semua orang di tempat lain — panel akan terkunci total begitu
     * dipasang di hosting.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->sudahDisetujui() && $this->peran->bolehMasukPanel();
    }

    public function adalahAdmin(): bool
    {
        return $this->peran === PeranPengguna::Admin;
    }

    public function sudahDisetujui(): bool
    {
        return $this->disetujui_pada !== null;
    }

    public function setujui(): void
    {
        $this->forceFill(['disetujui_pada' => now()])->save();
    }

    #[Scope]
    protected function menungguPersetujuan(Builder $query): Builder
    {
        return $query->whereNull('disetujui_pada');
    }
}
