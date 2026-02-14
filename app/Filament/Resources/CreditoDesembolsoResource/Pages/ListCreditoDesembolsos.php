<?php

namespace App\Filament\Resources\CreditoDesembolsoResource\Pages;

use App\Filament\Resources\CreditoDesembolsoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditoDesembolsos extends ListRecords
{
    protected static string $resource = CreditoDesembolsoResource::class;

    protected function getHeaderActions(): array
    {
        return [
        ];
    }
}
