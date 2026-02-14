<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\Pages;

use App\Filament\Asociado\Resources\EstadoCuentaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEstadoCuenta extends EditRecord
{
    protected static string $resource = EstadoCuentaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', auth()->user()->id);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
