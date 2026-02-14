<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers;


use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CreditoSolicitudesRelationManager extends RelationManager
{
    protected static string $relationship = 'creditoSolicitudes';

    public bool $show = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('observaciones')
            ->columns([
                Tables\Columns\TextColumn::make('solicitud')->label('Nro solicitud')->default('N/A'),
                Tables\Columns\TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'P' => 'gray',
                        'N' => 'danger',
                        'M' => 'gray',
                        'A' => 'success',
                        'C' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'P' => 'PENDIENTE',
                        'N' => 'NEGADA',
                        'M' => 'MONTO DESEMBOLSADO',
                        'A' => 'APROBADA',
                        'C' => 'CANCELADA',
                    }),
                Tables\Columns\TextColumn::make('linea')->label('Linea de credito')->default('N/A'),
                Tables\Columns\TextColumn::make('tasa_id')->label('Interes Corriente')->formatStateUsing(fn($state) => $state !== null ? number_format($state, 2) . ' %' : 'N/A')->default('N/A'),
                Tables\Columns\TextColumn::make('nro_cuotas_max')->label('Nro Cuotas')->default('N/A'),
                Tables\Columns\TextColumn::make('fecha_solicitud')->label('Fecha Solicitud')->default('N/A'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
            ])
            ->actions([
                //Tables\Actions\EditAction::make()->slideOver()->label('ver'),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->deferLoading()
            ->defaultSort('id', 'desc');
    }
}
