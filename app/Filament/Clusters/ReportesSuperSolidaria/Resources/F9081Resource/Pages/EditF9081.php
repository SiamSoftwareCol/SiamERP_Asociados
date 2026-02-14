<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9081Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9081Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9081 extends EditRecord
{
    protected static string $resource = F9081Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
