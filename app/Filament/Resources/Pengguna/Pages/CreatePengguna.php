<?php

namespace App\Filament\Resources\Pengguna\Pages;

use App\Filament\Resources\Pengguna\PenggunaResource;
use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreatePengguna extends CreateRecord
{
    protected static string $resource = PenggunaResource::class;

    /**
     * Akun yang dibuat Admin langsung disetujui — persetujuan hanya perlu
     * untuk orang yang mendaftar sendiri.
     */
    protected function handleRecordCreation(array $data): Model
    {
        /** @var User $pengguna */
        $pengguna = parent::handleRecordCreation($data);
        $pengguna->setujui();

        return $pengguna;
    }
}
