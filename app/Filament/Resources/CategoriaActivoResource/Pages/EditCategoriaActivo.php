<?php

namespace App\Filament\Resources\CategoriaActivoResource\Pages;

use App\Filament\Resources\CategoriaActivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaActivo extends EditRecord
{
    protected static string $resource = CategoriaActivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
