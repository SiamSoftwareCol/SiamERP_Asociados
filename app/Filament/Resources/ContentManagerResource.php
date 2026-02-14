<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContentManagerResource\Pages;
use App\Filament\Resources\ContentManagerResource\RelationManagers;
use App\Models\ContentManager;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContentManagerResource extends Resource
{
    protected static ?string $model = ContentManager::class;
    protected static ?string $navigationIcon = 'heroicon-o-cursor-arrow-ripple';
    protected static ?string $navigationLabel = 'Manejador de contenido';
    protected static ?string $navigationGroup = 'Configuración General';
    protected static ?string $modelLabel = 'Contenido';
    protected static ?string $pluralModelLabel = 'Contenidos';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
                Forms\Components\TextInput::make('name')
                    ->label('Nombre')
                    ->required()
                    ->autofocus()
                    ->placeholder('Ejemplo: Inicio')
                    ->hint('Nombre del contenido.'),
                Forms\Components\TextInput::make('title')
                    ->label('Título')
                    ->required()
                    ->placeholder('Ejemplo: Bienvenido a nuestro sitio web')
                    ->hint('Título del contenido.'),
                Forms\Components\Textarea::make('description')
                    ->label('Descripción')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull()
                    ->placeholder('Ejemplo: Bienvenido a nuestro sitio web')
                    ->hint('Breve descripción del contenido.'),
                Forms\Components\Select::make('is_active')
                    ->label('Estado')
                    ->options([
                        true => 'Activo',
                        false => 'Inactivo',
                    ])
                    ->searchable()
                    ->default('1')
                    ->required()
                    ->placeholder('Seleccione el estado del contenido')
                    ->hint('Estado del contenido.'),
                Forms\Components\Select::make('button')
                    ->label('Botón')
                    ->options([
                        '1' => 'Si tiene',
                        '0' => 'No tiene',
                    ])
                    ->searchable()
                    ->default('0')
                    ->required()
                    ->placeholder('Seleccione el estado del botón.'),
                Forms\Components\TextInput::make('button_text')
                    ->label('Texto del botón')
                    ->placeholder('Ejemplo: Leer más')
                    ->hint('Texto del botón.'),
                Forms\Components\TextInput::make('button_link')
                    ->label('URL del botón')
                    ->placeholder('Ejemplo: https://www.google.com')
                    ->hint('URL del botón.'),
                Forms\Components\TextInput::make('image')
                    ->label('Imagen')
                    ->placeholder('Ejemplo: https://drive.google.com/imagen.jpg')
                    ->hint('Imagen del contenido.'),

            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nombre'),
                Tables\Columns\TextColumn::make('title')
                    ->label('Título'),
                Tables\Columns\BooleanColumn::make('is_active')
                    ->label('Estado'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de creación')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListContentManagers::route('/'),
            //'create' => Pages\CreateContentManager::route('/create'),
            //'view' => Pages\ViewContentManager::route('/{record}'),
            //'edit' => Pages\EditContentManager::route('/{record}/edit'),
        ];
    }
}
