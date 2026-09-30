<?php

namespace App\Filament\Resources\Setorans\Pages;

use App\Filament\Resources\Setorans\SetoranResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSetoran extends CreateRecord
{
    protected static string $resource = SetoranResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['dicatat_oleh_user_id'] = auth()->id();

        return $data;
    }
}
