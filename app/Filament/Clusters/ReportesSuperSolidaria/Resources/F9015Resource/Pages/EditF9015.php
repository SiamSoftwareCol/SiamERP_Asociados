<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9015Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9015Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9015 extends EditRecord
{
    protected static string $resource = F9015Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
