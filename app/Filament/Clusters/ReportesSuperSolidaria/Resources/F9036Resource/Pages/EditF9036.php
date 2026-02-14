<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9036Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9036Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9036 extends EditRecord
{
    protected static string $resource = F9036Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
