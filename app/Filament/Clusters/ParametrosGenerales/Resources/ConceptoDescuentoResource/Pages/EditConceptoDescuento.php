<?php

namespace App\Filament\Clusters\ParametrosGenerales\Resources\ConceptoDescuentoResource\Pages;

use App\Filament\Clusters\ParametrosGenerales\Resources\ConceptoDescuentoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConceptoDescuento extends EditRecord
{
    protected static string $resource = ConceptoDescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
