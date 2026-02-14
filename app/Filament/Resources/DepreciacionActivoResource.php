<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ActivosFijos;
use App\Filament\Resources\DepreciacionActivoResource\Pages;
use App\Filament\Resources\DepreciacionActivoResource\RelationManagers;
use App\Models\DepreciacionActivo;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DepreciacionActivoResource extends Resource
{
    protected static ?string $model = DepreciacionActivo::class;
    protected static ?string    $cluster = ActivosFijos::class;
    protected static ?string    $navigationIcon = 'heroicon-o-paper-clip';
    protected static ?string    $navigationLabel = 'Depreciacion de Activos';
    protected static ?string    $navigationParentItem = 'Activos Fijos';
    protected static ?string    $modelLabel = 'Depreciacion de Activos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('activo_id')
                ->relationship('activo', 'nombre')
                ->label('Activo')
                ->searchable()
                ->required(),
            DatePicker::make('fecha')
                ->required(),
            TextInput::make('valor_depreciado')
                ->label('Valor depreciado')
                ->numeric()
                ->required(),
            TextInput::make('valor_acumulado')
                ->label('Depreciación acumulada')
                ->numeric()
                ->required(),
            TextInput::make('valor_en_libros')
                ->label('Valor en libros')
                ->numeric()
                ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListDepreciacionActivos::route('/'),
            'create' => Pages\CreateDepreciacionActivo::route('/create'),
            'edit' => Pages\EditDepreciacionActivo::route('/{record}/edit'),
        ];
    }
}
