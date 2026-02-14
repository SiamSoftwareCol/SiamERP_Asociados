<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9078Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9078Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9078 extends EditRecord
{
    protected static string $resource = F9078Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
