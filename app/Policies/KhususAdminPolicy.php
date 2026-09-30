<?php

namespace App\Policies;

use App\Models\User;

/**
 * Data induk — Tahun Ajaran, Kelas, Siswa, Projek, dan akun — hanya dikelola
 * Admin (PRD §6). Guru bekerja pada Catatan dan Penjualan, bukan pada fondasi
 * yang dipakai seluruh rekap.
 *
 * Satu kelas dasar, bukan lima salinan aturan yang sama: bila kelak Guru
 * boleh melihat daftar Siswa, cukup timpa viewAny() pada kebijakan Siswa.
 */
abstract class KhususAdminPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->adalahAdmin();
    }

    public function view(User $user): bool
    {
        return $user->adalahAdmin();
    }

    public function create(User $user): bool
    {
        return $user->adalahAdmin();
    }

    public function update(User $user): bool
    {
        return $user->adalahAdmin();
    }

    public function delete(User $user): bool
    {
        return $user->adalahAdmin();
    }

    public function deleteAny(User $user): bool
    {
        return $user->adalahAdmin();
    }
}
