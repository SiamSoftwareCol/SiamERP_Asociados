<?php

namespace App\Filament\Resources\ContentManagerResource\Pages;

use App\Filament\Resources\ContentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewContentManager extends ViewRecord
{
    protected static string $resource = ContentManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
