<?php
// app/Filament/Resources/CdatResource/RelationManagers/BeneficiariosRelationManager.php

namespace App\Filament\Resources\CdatResource\RelationManagers;

use App\Models\Tercero;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class BeneficiariosRelationManager extends RelationManager
{
    protected static string $relationship = 'beneficiarios';
    protected static ?string $title = 'Beneficiarios y Cotitulares';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tercero_id')
                    ->label('Tercero Beneficiario')
                    ->options(
                                Tercero::whereNotNull('nombre_completo')
                                    ->pluck('nombre_completo', 'tercero_id')
                            )
                    ->searchable()
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\Checkbox::make('principal')
                    ->label('Es Cotitular')
                    ->default(false),
                Forms\Components\Textarea::make('observaciones')
                    ->label('Observaciones')
                    ->nullable()
                    ->required()
                    ->validationMessages([
                        'required' => 'Debe ingresar una observación.'
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('tercero.nombre_completo')
            ->columns([
            Tables\Columns\TextColumn::make('tercero.nombre_completo')
                ->label('Nombre Beneficiario'),
            Tables\Columns\IconColumn::make('principal')
                ->label('Es Cotitular')
                ->color('primary')
                ->boolean(),
            Tables\Columns\TextColumn::make('observaciones')
                ->label('Observaciones')

                ->limit(50),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }
}
