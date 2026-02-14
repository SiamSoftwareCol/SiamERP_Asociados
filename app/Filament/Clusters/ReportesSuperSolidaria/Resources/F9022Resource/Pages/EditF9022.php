<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9022Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9022Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9022 extends EditRecord
{
    protected static string $resource = F9022Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
