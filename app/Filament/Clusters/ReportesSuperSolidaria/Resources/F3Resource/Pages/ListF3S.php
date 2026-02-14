<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F3Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F3Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListF3S extends ListRecords
{
    protected static string $resource = F3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
