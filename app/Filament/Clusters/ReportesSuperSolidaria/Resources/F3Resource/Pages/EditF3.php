<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F3Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F3Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF3 extends EditRecord
{
    protected static string $resource = F3Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
