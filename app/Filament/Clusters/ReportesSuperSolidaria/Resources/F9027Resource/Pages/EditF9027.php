<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9027Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9027Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9027 extends EditRecord
{
    protected static string $resource = F9027Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
