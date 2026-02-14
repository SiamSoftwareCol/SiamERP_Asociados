<?php

namespace App\Filament\Resources\CdatTipoResource\Pages;

use App\Filament\Resources\CdatTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCdatTipos extends ListRecords
{
    protected static string $resource = CdatTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
