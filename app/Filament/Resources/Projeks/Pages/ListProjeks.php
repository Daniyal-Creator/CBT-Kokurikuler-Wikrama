<?php

namespace App\Filament\Resources\Projeks\Pages;

use App\Filament\Resources\Projeks\ProjekResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListProjeks extends ListRecords
{
    protected static string $resource = ProjekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
