<?php

namespace App\Filament\Resources\CreditoConsultaResource\Pages;

use App\Filament\Resources\CreditoConsultaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCreditoConsultas extends ListRecords
{
    protected static string $resource = CreditoConsultaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
