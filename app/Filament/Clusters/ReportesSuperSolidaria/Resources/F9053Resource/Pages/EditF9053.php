<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9053Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9053Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9053 extends EditRecord
{
    protected static string $resource = F9053Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
