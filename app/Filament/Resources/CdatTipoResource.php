<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\InformeSaldosCdat;
use App\Filament\Resources\CdatTipoResource\Pages;
use App\Models\CdatTipo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Get;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;
use Filament\Support\Enums\Alignment;
use Filament\Forms\Components\Wizard;
use PhpParser\Node\Stmt\Label;

class CdatTipoResource extends Resource
{
    protected static ?string $model = CdatTipo::class;

    protected static ?string    $navigationIcon = 'heroicon-o-document-duplicate';
    protected static ?string    $cluster = InformeSaldosCdat::class;
    protected static ?string    $navigationLabel = 'Lineas de CDAT';
    protected static ?string    $navigationGroup = 'Parametros de CDAT';
    protected static ?string    $slug = 'Par/Tab/ParCdatTipos';
    protected static ?int       $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Información General y Configuración')
                        ->description('Datos básicos y valores principales del tipo de CDAT.')
                        ->schema([
                            Forms\Components\TextInput::make('nombre')
                                ->label('Nombre del Tipo de CDAT')
                                ->required()
                                ->maxLength(255)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('codigo_producto')
                                ->label('Código de Producto (Interno)')
                                ->maxLength(255)
                                ->unique(CdatTipo::class, 'codigo_producto', ignoreRecord: true)
                                ->columnSpan(1),
                            Forms\Components\Toggle::make('activo')
                                ->label('Activo')
                                ->required()
                                ->default(true)
                                ->inline(false)
                                ->columnSpan(1),
                            Forms\Components\Textarea::make('descripcion')
                                ->label('Descripción Detallada')
                                ->nullable()
                                ->columnSpanFull(),

                            Forms\Components\TextInput::make('cdat_plazo_id')
                                ->label('Plazo Principal Asociado')
                                ->nullable()
                                ->prefix('Dias')
                                ->placeholder('Seleccione un plazo principal')
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('cdat_tasa_id')
                                ->label('Tasa de Liquidación Principal')
                                ->nullable()
                                ->prefix('EA %')
                                ->placeholder('Seleccione la tasa de liquidación')
                                ->columnSpan(1),

                            Forms\Components\TextInput::make('valor_minimo')
                                ->label('Valor Mínimo de Apertura')
                                ->numeric()->inputMode('decimal')->step('1')->prefix('COP')
                                ->nullable()->minValue(0)->rules(['regex:/^\d{1,13}(\.\d{1,2})?$/'])
                                ->columnSpan(1),
                            Forms\Components\TextInput::make('valor_maximo')
                                ->label('Valor Máximo de Apertura (Opcional)')
                                ->numeric()->inputMode('decimal')->step('1')->prefix('COP')
                                ->nullable()->minValue(0)->rules(['regex:/^\d{1,13}(\.\d{1,2})?$/'])
                                ->gt('valor_minimo')
                                ->columnSpan(1),
                        ])->columns(4),

                    Wizard\Step::make('Políticas, Condiciones y Retenciones')
                        ->description('Define las reglas operativas y fiscales del tipo de CDAT.')
                        ->schema([
                            Forms\Components\TextInput::make('puc_contable')
                                ->label('PUC - Capital inicial ')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('puc_contable_interes')
                                ->label('PUC - Contabilidad Intereses ')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('puc_contable_retencion')
                                ->label('PUC - Retención en la fuente ')
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('puc_contable_causacion')
                                ->label('PUC - Causación Intereses ')
                                ->columnSpan(2),

                            Forms\Components\TextInput::make('porcentaje_penalizacion_cancelacion_anticipada')
                                ->label('% Cancelación Anticipada')
                                ->numeric()->inputMode('decimal')->step('0.01')->suffix('%')
                                ->nullable()->minValue(0)->maxValue(100)->rules(['regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                ->helperText('Sobre intereses o capital. Dejar vacío si no aplica.')
                                ->visible(fn(Get $get) => $get('permite_cancelacion_anticipada'))
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('permite_cancelacion_anticipada')
                                ->label('Cancelación Anticipada?')
                                ->required()->default(true)->live()->inline(false)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('dias_notificacion_previa_vencimiento')
                                ->label('Notificación Previa')
                                ->prefix('Dias Previo')
                                ->integer()->nullable()->minValue(0)
                                ->columnSpan(2),
                            Forms\Components\Toggle::make('permite_renovacion')
                                ->label('Permite Renovación')
                                ->required()->default(true)->inline(false)
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('base_minima_retencion_fuente')
                                ->label('Base Mínima para Ret. Fuente (COP)')
                                ->numeric()->inputMode('decimal')->step('0.01')->prefix('COP')
                                ->nullable()->minValue(0)->rules(['regex:/^\d{1,13}(\.\d{1,2})?$/'])
                                ->columnSpan(3),
                            Forms\Components\TextInput::make('porcentaje_retencion_fuente_rendimientos')
                                ->label('% Ret. Fuente')
                                ->numeric()->inputMode('decimal')->step('0.01')->suffix('%')
                                ->nullable()->minValue(0)->maxValue(100)->rules(['regex:/^\d{1,3}(\.\d{1,2})?$/'])
                                ->columnSpan(1),


                        ])->columns(8),
                ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo_producto')
                    ->alignment(Alignment::Center)
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->Label('Linea CDAT')
                    ->alignment(Alignment::Center)
                    ->searchable(),

                Tables\Columns\IconColumn::make('permite_renovacion')
                    ->Label('Renovables?')
                    ->alignment(Alignment::Center)
                    ->boolean(),
                Tables\Columns\IconColumn::make('permite_cancelacion_anticipada')
                    ->Label('Cancelacion Anticipada?')
                    ->alignment(Alignment::Center)
                    ->boolean(),
                Tables\Columns\IconColumn::make('activo')
                    ->Label('Activo')
                    ->alignment(Alignment::Center)
                    ->boolean(),
            ])
            ->filters([

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [

        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCdatTipos::route('/'),
            'create' => Pages\CreateCdatTipo::route('/create'),
            'edit' => Pages\EditCdatTipo::route('/{record}/edit'),
        ];
    }
}
