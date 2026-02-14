<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9999Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9999Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9999 extends EditRecord
{
    protected static string $resource = F9999Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
