<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9034Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9034Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9034 extends EditRecord
{
    protected static string $resource = F9034Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
