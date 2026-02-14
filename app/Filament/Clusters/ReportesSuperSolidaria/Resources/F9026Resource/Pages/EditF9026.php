<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9026Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9026Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9026 extends EditRecord
{
    protected static string $resource = F9026Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
