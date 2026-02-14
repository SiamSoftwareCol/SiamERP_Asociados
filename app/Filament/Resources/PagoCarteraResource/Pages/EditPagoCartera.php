<?php

namespace App\Filament\Resources\PagoCarteraResource\Pages;

use App\Filament\Resources\PagoCarteraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPagoCartera extends EditRecord
{
    protected static string $resource = PagoCarteraResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
