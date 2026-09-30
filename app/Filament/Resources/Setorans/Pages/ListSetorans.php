<?php

namespace App\Filament\Resources\Setorans\Pages;

use App\Filament\Resources\Setorans\SetoranResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSetorans extends ListRecords
{
    protected static string $resource = SetoranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
