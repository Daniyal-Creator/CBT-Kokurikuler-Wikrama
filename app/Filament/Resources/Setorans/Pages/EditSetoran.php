<?php

namespace App\Filament\Resources\Setorans\Pages;

use App\Filament\Resources\Setorans\SetoranResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSetoran extends EditRecord
{
    protected static string $resource = SetoranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
