<?php

namespace App\Filament\Asociado\Resources\PerfilResource\Pages;

use App\Filament\Asociado\Resources\PerfilResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPerfils extends ListRecords
{
    protected static string $resource = PerfilResource::class;

    protected static string $view = 'custom.asociados.profile';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\CreateAction::make(),
        ];
    }
}
