<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9999Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9999Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListF9999S extends ListRecords
{
    protected static string $resource = F9999Resource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
