<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F162Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F162Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF162 extends EditRecord
{
    protected static string $resource = F162Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
