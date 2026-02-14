<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\Filter;

class ObligacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'obligaciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('concepto')
            ->columns([
                Tables\Columns\TextColumn::make('fecha_vencimiento')->default('N/A'),
                Tables\Columns\TextColumn::make('con_descuento')->default('N/A'),
                Tables\Columns\TextColumn::make('descripcion_concepto')->default('N/A'),
                Tables\Columns\TextColumn::make('consecutivo')->default('N/A'),
                Tables\Columns\TextColumn::make('nro_cuota')->default('N/A'),
                Tables\Columns\TextColumn::make('vlr_cuota')->money('COP')->default('N/A'),
            ])
            ->filters([
                //
                Filter::make('vigente'),
                Filter::make('vencida'),
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([]);
    }
}
