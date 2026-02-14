<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9052Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9052Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9052 extends EditRecord
{
    protected static string $resource = F9052Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
