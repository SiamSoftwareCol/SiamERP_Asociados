<?php

namespace App\Filament\Resources\CdatResource\Pages;

use App\Filament\Resources\CdatResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCdats extends ListRecords
{
    protected static string $resource = CdatResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
