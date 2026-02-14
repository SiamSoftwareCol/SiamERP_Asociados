<?php

namespace App\Filament\Clusters\ParametrosGenerales\Resources;

use App\Filament\Clusters\ParametrosGenerales;
use App\Filament\Clusters\ParametrosGenerales\Resources\ConceptoDescuentoResource\Pages;
use App\Filament\Clusters\ParametrosGenerales\Resources\ConceptoDescuentoResource\RelationManagers;
use App\Models\ConceptoDescuento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ConceptoDescuentoResource extends Resource
{
    protected static ?string $model = ConceptoDescuento::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = ParametrosGenerales::class;


        protected static ?string    $navigationLabel = 'Transacciones del Sistema';
        protected static ?string    $navigationGroup = 'Parametros';
        protected static ?string    $navigationParentItem = 'Parametros Terceros';
        protected static ?string    $modelLabel = 'Transaccion';
        protected static ?string    $pluralModelLabel = 'Transacciones';
        protected static ?string    $slug = 'Par/Tab/Trans';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo_descuento')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('descripcion')
                    ->required()
                    ->maxLength(60),
                Forms\Components\TextInput::make('reservado')
                    ->maxLength(1)
                    ->default('N'),
                Forms\Components\TextInput::make('cuenta_contable')
                    ->maxLength(14),
                Forms\Components\TextInput::make('genera_interes_x_pagar')
                    ->maxLength(1)
                    ->default('N'),
                Forms\Components\TextInput::make('cuenta_interes')
                    ->maxLength(14),
                Forms\Components\TextInput::make('porcentaje_interes')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('cuenta_rete_fuente')
                    ->maxLength(14),
                Forms\Components\TextInput::make('porcentaje_rete_fuente')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('base_rete_fuente')
                    ->numeric()
                    ->default(0),
                Forms\Components\TextInput::make('identificador_concepto')
                    ->maxLength(2),
                Forms\Components\TextInput::make('transaccional')
                    ->maxLength(1),
                Forms\Components\TextInput::make('distribuye')
                    ->maxLength(1),
                Forms\Components\TextInput::make('genera_extracto')
                    ->maxLength(1),
                Forms\Components\TextInput::make('genera_cruce')
                    ->maxLength(1)
                    ->default('N'),
                Forms\Components\TextInput::make('obliga_retiro_total')
                    ->maxLength(1)
                    ->default('N'),
                Forms\Components\TextInput::make('porcentaje_interes_ef')
                    ->numeric()
                    ->default(0),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('codigo_descuento')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->searchable(),
                Tables\Columns\TextColumn::make('reservado')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cuenta_contable')
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListConceptoDescuentos::route('/'),
            'create' => Pages\CreateConceptoDescuento::route('/create'),
            'edit' => Pages\EditConceptoDescuento::route('/{record}/edit'),
        ];
    }
}
