<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ActivosFijos;
use App\Filament\Resources\ActivoResource\Pages;
use App\Filament\Resources\ActivoResource\RelationManagers;
use App\Models\Activo;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ActivoResource extends Resource
{
    protected static ?string $model = Activo::class;
    protected static ?string    $cluster = ActivosFijos::class;
    protected static ?string    $navigationIcon = 'heroicon-o-swatch';
    protected static ?string    $navigationLabel = 'Inventario de Activos';
    protected static ?string    $navigationParentItem = 'Activos Fijos';
    protected static ?string    $modelLabel = 'Inventario de Activos';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Grid::make(6)
                ->schema([
                    TextInput::make('codigo')
                        ->label('Código')
                        ->required()
                        ->unique(ignoreRecord: true),

                    Select::make('categoria_id')
                        ->label('Categoría')
                        ->columnSpan(2)
                        ->relationship('categoria', 'nombre')
                        ->required(),

                    TextInput::make('nombre')
                        ->label('Nombre del activo')
                        ->required()
                        ->maxLength(255)
                        ->columnSpan(3),

                    DatePicker::make('fecha_adquisicion')
                        ->label('Fecha de adquisición')
                        ->required(),

                    Select::make('estado')
                        ->label('Estado')
                        ->options([
                            'bueno' => 'Bueno',
                            'regular' => 'Regular',
                            'malo' => 'Malo',
                            'n/a' => 'No Aplica',
                        ])
                        ->default('activo'),

                    TextInput::make('valor_adquisicion')
                        ->label('Valor de adquisición')
                        ->numeric()
                        ->columnSpan(1)
                        ->required(),

                    TextInput::make('valor_residual')
                        ->label('Valor residual')
                        ->numeric()
                        ->columnSpan(1)
                        ->required(),

                    TextInput::make('vida_util_meses')
                        ->label('Vida útil (meses)')
                        ->numeric()
                        ->columnSpan(1)
                        ->required(),

                    Select::make('metodo_depreciacion')
                        ->label('Método de depreciación')
                        ->options([
                            'linea_recta' => 'Línea recta',
                            'declinacion_doble' => 'Declinación doble',
                            'suma_digitos' => 'Suma de dígitos',
                            'saldos_decrecientes' => 'Saldos decrecientes',
                            'saldos_crecientes' => 'Saldos crecientes'
                        ])
                        ->required()
                        ->columnSpan(2),

                    DatePicker::make('ultima_depreciacion')
                        ->label('Última depreciación')
                        ->nullable(),

                    TextInput::make('ubicacion')
                        ->label('Ubicación')
                        ->columnSpan(2)
                        ->nullable(),

                    Select::make('responsable_id')
                        ->relationship(
                            'responsable',
                            'name',
                            fn (Builder $query) => $query->where('canview', 'administracion')
                        )
                        ->searchable()
                        ->preload()
                        ->columnSpan(2)
                        ->nullable(),
                    Textarea::make('descripcion')
                        ->label('Descripcion del Activo')
                        ->columnSpanFull()
                        ->nullable(),
                ]),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->heading('Invetario de Activos')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre del activo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'bueno' => 'primary',
                        'regular' => 'warning',
                        'malo' => 'danger',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('valor_adquisicion')
                    ->label('Valor')
                    ->money('COP'),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación'),

            ])

            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListActivos::route('/'),
            'create' => Pages\CreateActivo::route('/create'),
            'edit' => Pages\EditActivo::route('/{record}/edit'),
        ];
    }
}
