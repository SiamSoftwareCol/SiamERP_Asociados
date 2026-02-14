<?php

namespace App\Filament\Resources\CdatResource\Pages;

use App\Filament\Resources\CdatResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditCdat extends EditRecord
{
    protected static string $resource = CdatResource::class;
    protected static ?string $title = 'CDAT - Condiciones del Titulo y Beneficiarios';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('volver')
                ->label('Regresar')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('Slate'),
        ];
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function canSave(): bool
    {
        return false;
    }


    protected function getRedirectUrl(): string
    {
        return static::getResource()::getUrl('index');
    }
}
