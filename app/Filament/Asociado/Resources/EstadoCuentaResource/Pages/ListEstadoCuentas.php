<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\Pages;

use App\Filament\Asociado\Resources\EstadoCuentaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEstadoCuentas extends ListRecords
{
    protected static string $resource = EstadoCuentaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
