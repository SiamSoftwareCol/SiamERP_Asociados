<?php

namespace App\Filament\Resources\ReporteActivoResource\Pages;

use App\Filament\Resources\ReporteActivoResource;
use Filament\Resources\Pages\ListRecords;

class ListReporteActivos extends ListRecords
{
    protected static string $resource = ReporteActivoResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
