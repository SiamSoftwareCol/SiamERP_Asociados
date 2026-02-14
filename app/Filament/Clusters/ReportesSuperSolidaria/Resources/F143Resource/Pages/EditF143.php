<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F143Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F143Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF143 extends EditRecord
{
    protected static string $resource = F143Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
