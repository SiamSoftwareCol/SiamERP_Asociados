<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F130Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F130Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListF130S extends ListRecords
{
    protected static string $resource = F130Resource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
