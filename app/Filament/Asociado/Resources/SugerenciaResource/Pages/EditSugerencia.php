<?php

namespace App\Filament\Asociado\Resources\SugerenciaResource\Pages;

use App\Filament\Asociado\Resources\SugerenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSugerencia extends EditRecord
{
    protected static string $resource = SugerenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\ViewAction::make(),
            //Actions\DeleteAction::make(),
        ];
    }
}
