<?php

namespace App\Filament\Resources\ConceptoDescuentoResource\Pages;

use App\Filament\Resources\ConceptoDescuentoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConceptoDescuentos extends ListRecords
{
    protected static string $resource = ConceptoDescuentoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
