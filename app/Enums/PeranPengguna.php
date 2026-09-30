<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PeranPengguna: string implements HasLabel
{
    case Admin = 'admin';

    case Guru = 'guru';

    public function getLabel(): string
    {
        return match ($this) {
            self::Admin => 'Admin',
            self::Guru => 'Guru',
        };
    }

    /**
     * Petugas sengaja tidak ada di sini.
     *
     * Petugas adalah Siswa yang masuk lewat NIS dan bekerja di halaman input
     * tersendiri, bukan di panel Filament. Menaruhnya dalam enum ini akan
     * mengaburkan batas itu.
     */
    public function bolehMasukPanel(): bool
    {
        return true;
    }
}
