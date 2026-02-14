<?php

namespace App\Filament\Resources\CancelacionCdatResource\Pages;

use App\Filament\Resources\CancelacionCdatResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCancelacionCdat extends EditRecord
{
    protected static string $resource = CancelacionCdatResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
