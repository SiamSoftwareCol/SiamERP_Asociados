<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9013Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9013Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9013 extends EditRecord
{
    protected static string $resource = F9013Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
