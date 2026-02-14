<?php

namespace App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers;

use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class CertificadoDepositosRelationManager extends RelationManager
{
    protected static string $relationship = 'certificadoDepositos';

    protected static ?string $title = 'CDATs';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tasa')
            ->columns([
                Tables\Columns\TextColumn::make('numeronumero_cdat_cdt')->label('Número CDAT'),
                Tables\Columns\TextColumn::make('valor')->label('Valor'),
                Tables\Columns\TextColumn::make('plazo')->label('Plazo'),
                Tables\Columns\TextColumn::make('tasa_ea')->label('Tasa EA'),
                Tables\Columns\TextColumn::make('fecha_creacion')->label('Fecha Creación'),
                Tables\Columns\TextColumn::make('fecha_vencimiento')->label('Fecha Vencimiento'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
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
