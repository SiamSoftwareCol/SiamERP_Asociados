<?php

namespace App\Filament\Resources\CreditoRegeneracionResource\Pages;

use App\Filament\Resources\CreditoRegeneracionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditoRegeneracions extends ListRecords
{
    protected static string $resource = CreditoRegeneracionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
