<?php

namespace App\Filament\Resources\CreditoConsultaResource\Pages;

use App\Filament\Resources\CreditoConsultaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCreditoConsulta extends EditRecord
{
    protected static string $resource = CreditoConsultaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
