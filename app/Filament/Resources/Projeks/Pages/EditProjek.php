<?php

namespace App\Filament\Resources\Projeks\Pages;

use App\Filament\Resources\Projeks\ProjekResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditProjek extends EditRecord
{
    protected static string $resource = ProjekResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
