<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ParametrosGenerales;
use App\Filament\Resources\AsesoreResource\Pages;
use App\Filament\Resources\AsesoreResource\RelationManagers;
use App\Models\Asesor;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AsesoreResource extends Resource
{
    protected static ?string    $model = Asesor::class;
    protected static ?string    $cluster = ParametrosGenerales::class;
    protected static ?string    $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string    $recordTitleAttribute = 'nombre';
    protected static ?string    $navigationGroup = 'Parametros';
    protected static ?string    $navigationParentItem = 'Parametros Asociados';
    protected static ?string    $modelLabel = 'Asesores Comericlaes';
    protected static ?string    $pluralModelLabel = 'Asesores Comerciales';
    protected static ?string    $slug = 'Par/Tab/Asesores';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('codigo_asesor')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('nombre')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('descripcion')
                    ->nullable()
                    ->columnSpanFull(),
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
            'index' => Pages\ListAsesores::route('/'),
            'create' => Pages\CreateAsesore::route('/create'),
            'edit' => Pages\EditAsesore::route('/{record}/edit'),
        ];
    }
}
