<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ParametrosCartera; // Asegúrate de que la ruta coincida
use App\Filament\Resources\ConceptoDescuentoResource\Pages;
use App\Models\ConceptoDescuento;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Get;

class ConceptoDescuentoResource extends Resource
{
    protected static ?string $model = ConceptoDescuento::class;

    // Integración con tu Cluster
    protected static ?string $cluster = ParametrosCartera::class;
    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Conceptos de Descuento';
    protected static ?string $modelLabel = 'Concepto de Descuento';
    protected static ?string $navigationGroup = 'Parametros de Cartera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información Básica')
                    ->columns(4)
                    ->schema([
                        Forms\Components\TextInput::make('codigo_descuento')
                            ->label('Código')
                            ->required()
                            ->numeric()
                            ->columnSpan(1)
                            ->default(fn() => (ConceptoDescuento::max('codigo_descuento') ?? 0) + 1)
                            ->readOnly()
                            ->unique('concepto_descuentos', 'codigo_descuento', ignoreRecord: true),

                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->maxLength(60)
                            ->columnSpan(3)
                            ->extraInputAttributes(['style' => 'text-transform: uppercase;'])
                            ->dehydrateStateUsing(fn($state) => strtoupper($state)),

                        Forms\Components\Select::make('identificador_concepto')
                            ->label('Tipo de Concepto')
                            ->required()
                            ->options([
                                'OT' => 'Otros Descuentos',
                                'PG' => 'Concepto de Pagares',
                                'AP' => 'Descuentos por Aportes',
                                'AH' => 'Concepto de Ahorros',
                                'HC' => 'Concepto de Honorarios y Comisiones',
                            ]),

                        Forms\Components\Select::make('reservado')
                            ->label('Reservado')
                            ->options(['S' => 'SI', 'N' => 'NO'])
                            ->default('N')
                            ->required(),

                        // Buscador en tabla PUCS
                        Forms\Components\Select::make('cuenta_contable')
                            ->label('Cuenta Contable')
                            ->searchable()
                            ->options(function () {
                                return DB::table('pucs')
                                    ->where('movimiento', true)
                                    ->pluck('puc', 'puc'); // Muestra el código y guarda el código
                            }),
                    ]),

                Forms\Components\Tabs::make('Configuraciones Avanzadas')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make('Intereses')
                            ->columns(2)
                            ->schema([
                                Forms\Components\Select::make('genera_interes_x_pagar')
                                    ->label('¿Genera Interés x Pagar?')
                                    ->options(['S' => 'SI', 'N' => 'NO'])
                                    ->default('N'),

                                Forms\Components\Select::make('cuenta_interes')
                                    ->label('Cuenta Interés')
                                    ->searchable()
                                    ->options(fn() => DB::table('pucs')->where('movimiento', true)->pluck('puc', 'puc')),

                                Forms\Components\TextInput::make('porcentaje_interes')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\TextInput::make('porcentaje_interes_ef')
                                    ->label('Interés Efectivo')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Tabs\Tab::make('Retenciones')
                            ->columns(3)
                            ->schema([
                                Forms\Components\Select::make('cuenta_rete_fuente')
                                    ->label('Cuenta ReteFuente')
                                    ->searchable()
                                    ->options(fn() => DB::table('pucs')->where('movimiento', true)->pluck('puc', 'puc')),

                                Forms\Components\TextInput::make('porcentaje_rete_fuente')
                                    ->numeric()
                                    ->default(0),

                                Forms\Components\TextInput::make('base_rete_fuente')
                                    ->numeric()
                                    ->default(0),
                            ]),

                        Forms\Components\Tabs\Tab::make('Operaciones')
                            ->columns(3)
                            ->schema([
                                // Helper para crear los selects SI/NO repetitivos
                                self::getSiNoSelect('transaccional'),
                                self::getSiNoSelect('distribuye'),
                                self::getSiNoSelect('genera_extracto'),
                                self::getSiNoSelect('genera_cruce'),
                                self::getSiNoSelect('obliga_retiro_total'),
                            ]),
                    ])->columnSpanFull(),
            ]);
    }

    protected static function getSiNoSelect(string $name): Forms\Components\Select
    {
        return Forms\Components\Select::make($name)
            ->label(str_replace('_', ' ', ucwords($name, '_')))
            ->options(['S' => 'SI', 'N' => 'NO'])
            ->default('N')
            ->required();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_descuento')
                    ->label('Código')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable(),
                Tables\Columns\TextColumn::make('cuenta_contable')
                    ->label('Cuenta'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ]);
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
