<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F173Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F173Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListF173S extends ListRecords
{
    protected static string $resource = F173Resource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
