<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F143Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F143Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListF143S extends ListRecords
{
    protected static string $resource = F143Resource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
