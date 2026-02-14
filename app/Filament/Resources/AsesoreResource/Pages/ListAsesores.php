<?php

namespace App\Filament\Resources\AsesoreResource\Pages;

use App\Filament\Resources\AsesoreResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAsesores extends ListRecords
{
    protected static string $resource = AsesoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
