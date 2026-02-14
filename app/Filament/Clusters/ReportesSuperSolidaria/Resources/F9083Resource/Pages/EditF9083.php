<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9083Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9083Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9083 extends EditRecord
{
    protected static string $resource = F9083Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
