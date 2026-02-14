<?php

namespace App\Filament\Resources\TasaResource\Pages;

use App\Filament\Resources\TasaResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTasa extends CreateRecord
{
    protected static string $resource = TasaResource::class;


        protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }


}
