<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;

class GarantiasRelationManager extends RelationManager
{
    protected static string $relationship = 'garantias';

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
            ->recordTitleAttribute('altura_mora')
            ->columns([
                Tables\Columns\TextColumn::make('tipo_garantia_id')->label('Tipo garantia')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'P' => 'PERSONAL',
                        'R' => 'REAL',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'P' => 'info',
                        'R' => 'primary',
                    })
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('tercero.nombre_completo')->label('Tercero garantia')->default('N/A'),
                Tables\Columns\TextColumn::make('estado')->label('Estado garantia')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'A' => 'ACTIVO',
                        'C' => 'CANCELADA',
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'A' => 'primary',
                        'C' => 'danger',
                    })
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('nro_escr_o_matri')
                    ->label('Nro escritura / Matrícula')
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('fecha_avaluo')
                    ->default('N/A'),
                Tables\Columns\TextColumn::make('valor_avaluo')->label('Valor avaluo')->money('COP')
                    ->alignment(Alignment::End)
                    ->default(0),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->actions([
                //Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                /* Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]), */]);
    }
}
