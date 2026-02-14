<?php

namespace App\Filament\Clusters\Tesoreria\Resources\PagoEncabezadoResource\Pages;

use App\Filament\Clusters\Tesoreria\Resources\PagoEncabezadoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPagoEncabezado extends EditRecord
{
    protected static string $resource = PagoEncabezadoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
