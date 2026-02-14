<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources\F8888Resource\Pages;

use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F8888Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditF8888 extends EditRecord
{
    protected static string $resource = F8888Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
