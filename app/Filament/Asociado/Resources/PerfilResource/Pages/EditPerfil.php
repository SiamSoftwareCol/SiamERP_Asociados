<?php

namespace App\Filament\Asociado\Resources\PerfilResource\Pages;

use App\Filament\Asociado\Resources\PerfilResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPerfil extends EditRecord
{
    protected static string $resource = PerfilResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
