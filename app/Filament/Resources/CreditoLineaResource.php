<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ParametrosCartera;
use App\Filament\Resources\CreditoLineaResource\Pages;
use App\Filament\Resources\CreditoLineaResource\RelationManagers;
use App\Models\ClasificacionCredito;
use App\Models\CreditoLinea;
use App\Models\Moneda;
use App\Models\Puc;
use App\Models\Subcentro;
use App\Models\TipoGarantia;
use App\Models\TipoInversion;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditoLineaResource extends Resource
{
    protected static ?string $model = CreditoLinea::class;
    protected static ?string $cluster = ParametrosCartera::class;

    protected static ?string $navigationIcon = 'heroicon-o-adjustments-vertical';
    protected static ?string $navigationLabel = ' Lineas de credito';
    protected static ?string $modelLabel = 'Parametros - Linea de credito';
    protected static ?string $navigationGroup = 'Parametros de Cartera';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // SECCIÓN 1: Información Principal
                Section::make('Información Principal')
                    ->description('Datos básicos para identificar la línea de crédito.')
                    ->schema([
                        Forms\Components\TextInput::make('linea')
                            ->numeric()
                            ->label('Cod. Linea')
                            ->autocomplete(false)
                            ->required(),
                        Forms\Components\Select::make('clasificacion_id')->label('Clasificación')
                            ->relationship('clasificacion', 'descripcion')
                            ->searchable()
                            ->required()
                            ->createOptionForm([ /* Tu formulario de creación anidado */]),
                        Forms\Components\TextInput::make('descripcion')
                            ->label('Descripción')
                            ->required()
                            ->autocomplete(false)
                            ->columnSpan('full'), // Ocupa todo el ancho
                    ])->columns(2),

                // SECCIÓN 2: Condiciones Financieras
                Section::make('Condiciones Financieras')
                    ->description('Define los parámetros de tasas, montos y pagos.')
                    ->schema([
                        Forms\Components\TextInput::make('interes_cte')
                            ->label('Interés Corriente (%)')
                            ->numeric()
                            ->suffix('%')
                            ->required(),
                        Forms\Components\TextInput::make('interes_mora')
                            ->label('Interés de Mora (%)')
                            ->numeric()
                            ->suffix('%')
                            ->required(),
                        Forms\Components\Select::make('moneda_id')->label('Moneda')
                            ->relationship('moneda', 'nombre')
                            ->searchable()
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('monto_min')->label('Monto Mínimo')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('$'),
                        Forms\Components\TextInput::make('monto_max')->label('Monto Máximo')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('$'),
                        Forms\Components\Select::make('periodo_pago')->label('Periodo de Pago')
                            ->options(['15' => 'Quincenal', '30' => 'Mensual', '90' => 'Trimestral', '180' => 'Semestral', '360' => 'Anual', '120' => 'Cuatrimestral'])
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('tipo_cuota')->label('Tipo de Cuota')
                            ->options(['F' => 'Fija', 'V' => 'Variable'])
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('tipo_tasa')->label('Tipo de Tasa')
                            ->options(['A' => 'Anticipada', 'V' => 'Vencida'])
                            ->native(false)
                            ->required(),
                        Forms\Components\Select::make('abonos_extra')->label('Permite Abonos Extra')
                            ->options(['N' => 'No', 'S' => 'Si'])
                            ->native(false),
                    ])->columns(3),

                Section::make('Plazos y Garantías')
                    ->description('Establece los límites de cuotas y los requisitos de garantía.')
                    ->schema([
                        Forms\Components\TextInput::make('nro_cuotas_max')->label('Nro. Cuotas Máximo')->numeric(),
                        Forms\Components\TextInput::make('nro_cuotas_gracia')->label('Nro. Cuotas de Gracia')->numeric(),
                        Forms\Components\Select::make('tipo_garantia_id')->label('Tipo de Garantía')
                            ->relationship('tipoGarantia', 'nombre')
                            ->searchable()
                            ->required()
                            ->createOptionForm([ /* Tu formulario de creación anidado */]),
                        Forms\Components\TextInput::make('cant_gar_real')->label('Cantidad Garantía Real')->numeric(),
                        Forms\Components\TextInput::make('cant_gar_pers')->label('Cantidad Garantía Personal')->numeric(),
                        Forms\Components\Select::make('tipo_inversion_id')->label('Tipo de Inversión')
                            ->relationship('tipoInversion', 'descripcion')
                            ->searchable()
                            ->required()
                            ->createOptionForm([ /* Tu formulario de creación anidado */]),
                    ])->columns(3),

                // SECCIÓN 4: Clasificación Interna
                Section::make('Clasificación Interna')
                    ->schema([
                        Forms\Components\TextInput::make('ciius')->label('Código CIIU')->autocomplete(false),
                        Forms\Components\Select::make('subcentro_id')->label('Subcentro')
                            ->relationship('subcentro', 'descripcion')
                            ->searchable()
                            ->required()
                            ->native(false),
                    ])->columns(2),

            ])->columns(1); // Esto asegura que las secciones se apilen verticalmente
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('linea'),
                Tables\Columns\TextColumn::make('descripcion'),
                Tables\Columns\TextColumn::make('clasificacion.descripcion'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCreditoLineas::route('/'),
            'create' => Pages\CreateCreditoLinea::route('/create'),
            'edit' => Pages\EditCreditoLinea::route('/{record}/edit'),
        ];
    }
}
