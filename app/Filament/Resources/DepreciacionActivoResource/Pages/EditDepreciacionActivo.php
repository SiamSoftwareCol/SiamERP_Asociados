<?php

namespace App\Filament\Resources\DepreciacionActivoResource\Pages;

use App\Filament\Resources\DepreciacionActivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDepreciacionActivo extends EditRecord
{
    protected static string $resource = DepreciacionActivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
