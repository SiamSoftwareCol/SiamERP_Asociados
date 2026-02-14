<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ActivosFijos;
use App\Filament\Resources\CategoriaActivoResource\Pages;
use App\Filament\Resources\CategoriaActivoResource\RelationManagers;
use App\Models\CategoriaActivo;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CategoriaActivoResource extends Resource
{
    protected static ?string    $model = CategoriaActivo::class;
    protected static ?string    $cluster = ActivosFijos::class;
    protected static ?string    $navigationIcon = 'heroicon-c-arrow-right';
    protected static ?string    $navigationLabel = 'Categorías de Activos';
    protected static ?string    $navigationParentItem = 'Activos Fijos';
    protected static ?string    $modelLabel = 'Categorías de Activos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('nombre')->required()->maxLength(255),
                TextInput::make('vida_util_defecto')
                    ->label('Vida útil (meses)')
                    ->numeric()
                    ->required(),
                Select::make('metodo_depreciacion_defecto')
                    ->label('Método de Depreciación')

                    ->options([
                        'linea_recta' => 'Línea recta',
                        'declinacion_doble' => 'Declinación doble',
                        'suma_digitos' => 'Suma de dígitos',
                        'saldos_decrecientes' => 'Saldos decrecientes',
                        'saldos_crecientes' => 'Saldos crecientes'
                    ])
                    ->required(),
                TextInput::make('cuenta_contable')
                    ->label('Cuenta contable')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('nombre')
                ->searchable()
                ->sortable(),
                TextColumn::make('vida_util_defecto')
                ->label('Vida útil (meses)')
                ->sortable(),
                TextColumn::make('metodo_depreciacion_defecto')
                ->label('Método de Depreciación')
                ->sortable(),

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
            'index' => Pages\ListCategoriaActivos::route('/'),
            'create' => Pages\CreateCategoriaActivo::route('/create'),
            'edit' => Pages\EditCategoriaActivo::route('/{record}/edit'),
        ];
    }
}
