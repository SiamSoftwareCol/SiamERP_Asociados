<?php

namespace App\Filament\Asociado\Resources;

use App\Filament\Asociado\Resources\SugerenciaResource\Pages;
use App\Filament\Asociado\Resources\SugerenciaResource\RelationManagers;
use App\Models\Sugerencia;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SugerenciaResource extends Resource
{
    protected static ?string $model = Sugerencia::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Sugerencia')
                    ->icon('heroicon-o-inbox-arrow-down')
                    ->description('Escribe tu sugerencia')
                    ->columns(1)
                    ->schema([
                        Forms\Components\Textarea::make('sugerencia')
                            ->required()
                            ->columnSpanFull(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('sugerencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime()
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListSugerencias::route('/'),
            'create' => Pages\CreateSugerencia::route('/create'),
            'view' => Pages\ViewSugerencia::route('/{record}'),
            //'edit' => Pages\EditSugerencia::route('/{record}/edit'),
        ];
    }
}
