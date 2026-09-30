<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy extends KhususAdminPolicy
{
    /**
     * Admin tidak dapat menghapus akunnya sendiri — cara paling cepat
     * membuat sekolah kehilangan satu-satunya orang yang dapat mengelola akun.
     */
    public function delete(User $user, ?User $akun = null): bool
    {
        return $user->adalahAdmin() && ! $user->is($akun);
    }
}
