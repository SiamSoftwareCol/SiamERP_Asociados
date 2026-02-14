<?php

namespace App\Filament\Resources\PagoCarteraResource\Pages;

use App\Filament\Resources\PagoCarteraResource;
use Filament\Resources\Pages\ListRecords;

class ListPagoCarteras extends ListRecords
{
    protected static string $resource = PagoCarteraResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

}


