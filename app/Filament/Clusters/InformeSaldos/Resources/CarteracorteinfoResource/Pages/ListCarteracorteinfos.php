<?php

namespace App\Filament\Clusters\InformeSaldos\Resources\CarteracorteinfoResource\Pages;

use App\Filament\Clusters\InformeSaldos\Resources\CarteracorteinfoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCarteracorteinfos extends ListRecords
{
    protected static string $resource = CarteracorteinfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
