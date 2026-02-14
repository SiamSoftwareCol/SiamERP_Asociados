<?php

namespace App\Filament\Resources\TasaResource\Pages;

use App\Filament\Resources\TasaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTasa extends EditRecord
{
    protected static string $resource = TasaResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];

    }


        protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }


}
