<?php

namespace App\Filament\Asociado\Resources\SugerenciaResource\Pages;

use App\Filament\Asociado\Resources\SugerenciaResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewSugerencia extends ViewRecord
{
    protected static string $resource = SugerenciaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //Actions\EditAction::make(),
        ];
    }
}
