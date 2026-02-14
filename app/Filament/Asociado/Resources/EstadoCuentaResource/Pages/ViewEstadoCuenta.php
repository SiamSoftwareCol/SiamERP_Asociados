<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\Pages;

use App\Filament\Asociado\Resources\EstadoCuentaResource;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewEstadoCuenta extends ViewRecord
{
    protected static string $resource = EstadoCuentaResource::class;

    protected static string $view = 'custom.asociados.estado-cuenta';

    protected function getHeaderActions(): array
    {
        return [
            //Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('codigo_interno_pag')
                    ->label('Nro Identificacion asociado')
                    ->columns(2),
                TextEntry::make('tercero.nombres')
                    ->label('Nombre de Asociado')
                    ->placeholder('Nombre de Asociado')
                    ->columns(2),
                TextEntry::make('EstadoCliente.nombre')
                    ->label('Estado')
                    ->placeholder('Estado')
                    ->columns(2),
                TextEntry::make('pagaduria.nombre')
                    ->label('Pagaduria')
                    ->placeholder('Pagaduria')
                    ->columns(2),
            ]);
    }
}
