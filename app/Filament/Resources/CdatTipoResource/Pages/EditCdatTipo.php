<?php

namespace App\Filament\Resources\CdatTipoResource\Pages;

use App\Filament\Resources\CdatTipoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCdatTipo extends EditRecord
{
    protected static string $resource = CdatTipoResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }

        protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
