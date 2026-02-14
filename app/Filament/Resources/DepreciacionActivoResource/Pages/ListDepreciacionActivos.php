<?php

namespace App\Filament\Resources\DepreciacionActivoResource\Pages;

use App\Filament\Resources\DepreciacionActivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDepreciacionActivos extends ListRecords
{
    protected static string $resource = DepreciacionActivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
