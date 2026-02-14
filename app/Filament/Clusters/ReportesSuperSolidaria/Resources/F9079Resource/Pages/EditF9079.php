<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9079Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9079Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF9079 extends EditRecord
{
    protected static string $resource = F9079Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
