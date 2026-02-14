<?php

namespace App\Filament\Resources\ContentManagerResource\Pages;

use App\Filament\Resources\ContentManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\DB;

class ListContentManagers extends ListRecords
{
    protected static string $resource = ContentManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->visible(
                fn() => DB::table('content_managers')->count() < 0
            ),
        ];
    }
}
