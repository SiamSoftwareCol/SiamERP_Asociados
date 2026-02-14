<?php

namespace App\Filament\Resources\AsesoreResource\Pages;

use App\Filament\Resources\AsesoreResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAsesore extends EditRecord
{
    protected static string $resource = AsesoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
