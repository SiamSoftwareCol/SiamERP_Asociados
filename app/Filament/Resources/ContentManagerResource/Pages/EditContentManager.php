<?php

namespace App\Filament\Resources\ContentManagerResource\Pages;

use App\Filament\Resources\ContentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditContentManager extends EditRecord
{
    protected static string $resource = ContentManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
