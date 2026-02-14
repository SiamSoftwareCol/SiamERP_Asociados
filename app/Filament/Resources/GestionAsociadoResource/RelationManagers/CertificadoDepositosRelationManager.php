<?php

namespace App\Filament\Resources\GestionAsociadoResource\RelationManagers;

use App\Models\Barrio;
use App\Models\Beneficiario;
use App\Models\CdatTipo;
use App\Models\Ciudad;
use App\Models\Tercero;
use App\Models\TipoDocumentoContable;
use App\Models\TipoIdentificacion;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Enums\Alignment;
use Filament\Tables;
use Filament\Tables\Actions\Action as ActionsTable;
use Filament\Tables\Columns\ColumnGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CertificadoDepositosRelationManager extends RelationManager
{
    protected static string $relationship = 'certificadoDepositos';
    protected static ?string $title = 'CDATs';

    public bool $ownerDataUpdated = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Constituir CDAT')
                    ->description('Creación de registro')
                    ->icon('heroicon-m-user')
                    ->schema([
                        Forms\Components\Hidden::make('user_id')
                            ->default('admin'),

                        Forms\Components\Select::make('linea_captacion')
                            ->label('Linea CDAT')
                            ->native(false)
                            ->searchable()
                            ->columnSpan(3)
                            ->options(
                                CdatTipo::query()
                                    ->select(DB::raw("CONCAT(nombre) AS descripcion1"), 'id')
                                    ->pluck('descripcion1', 'id')
                                    ->toArray()
                            )
                            ->live()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $linea = CdatTipo::find($state);
                                if ($linea) {
                                    $set('valor', $linea->valor_maximo);
                                    $set('plazo', $linea->cdat_plazo_id);
                                    $set('tasa_ea', $linea->cdat_tasa_id);
                                    $set('retencion', $linea->porcentaje_retencion_fuente_rendimientos);
                                    $set('tasa_interes', round((pow(1 + ($linea->cdat_tasa_id / 100), 1 / 12) - 1) * 100, 4));
                                }

                                $doc = TipoDocumentoContable::where('sigla', 'CDT')->first();
                                if ($doc) {
                                    $nuevoNumero = $doc->numerador + 1;
                                    $set('numero_cdat', $nuevoNumero);
                                }
                            }),

                        Forms\Components\TextInput::make('numero_cdat')
                            ->label('Número CDAT')
                            ->required()
                            ->autocomplete(false)
                            ->columnSpan(2)
                            ->unique(table: 'cdats', column: 'numero_cdat')
                            ->validationMessages(['unique' => 'Este número de CDAT ya existe. Por favor, verifica la información.',])
                            ->rule('regex:/^[0-9]+$/'),

                        Forms\Components\TextInput::make('valor')
                            ->label('Valor Inicial')
                            ->required()
                            ->numeric()
                            ->columnSpan(2)
                            ->autocomplete(false),

                        Forms\Components\TextInput::make('tasa_ea')
                            ->label('Tasa E.A. (%)')
                            ->required()
                            ->numeric()
                            ->inputMode('decimal')
                            ->step('0.0001')
                            ->columnSpan(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set) {
                                if (is_numeric($state)) {
                                    $set('tasa_interes', round((pow(1 + ($state / 100), 1 / 12) - 1) * 100, 4));
                                }
                            }),
                        Forms\Components\TextInput::make('tasa_interes')
                            ->label('Tasa Interés (%)')
                            ->columnSpan(1)
                            ->readOnly(),

                        Forms\Components\TextInput::make('retencion')
                            ->label('Retencion (%)')
                            ->columnSpan(1)
                            ->readOnly(),
                        Forms\Components\TextInput::make('plazo')
                            ->label('Plazo Inversión (días)')
                            ->required()
                            ->integer()
                            ->numeric()
                            ->minValue(1)
                            ->columnSpan(1)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $fechaCreacion = $get('fecha_creacion');
                                if ($fechaCreacion && is_numeric($state)) {
                                    $fechaVencimiento = Carbon::parse($fechaCreacion)->addDays((int) $state);
                                    $set('fecha_vencimiento', $fechaVencimiento->format('Y-m-d'));
                                }
                            }),
                        Forms\Components\DatePicker::make('fecha_creacion')
                            ->label('Fecha Creación')
                            ->required()
                            ->live(onBlur: true)
                            ->native(false)
                            ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                $plazo = $get('plazo');
                                if ($state && is_numeric($plazo)) {
                                    $fechaVencimiento = Carbon::parse($state)->addDays((int) $plazo);
                                    $set('fecha_vencimiento', $fechaVencimiento->format('Y-m-d'));
                                }
                            }),

                        Forms\Components\DatePicker::make('fecha_vencimiento')
                            ->label('Fecha Vencimiento')
                            ->readOnly()
                            ->native(false),

                        Forms\Components\Select::make('pago_interes')
                            ->label('Pago Intereses')
                            ->options([
                                'mensual_vencido' => 'Mensual Vencido',
                                'termino_titulo' => 'Término del Título',
                            ])
                            ->placeholder('')
                            ->required()
                            ->columnSpan(2)
                            ->native(false),

                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->nullable()
                            ->columnSpanFull()
                            ->autocomplete(false),

                        Forms\Components\Hidden::make('estado')->default('P'),

                        Forms\Components\Hidden::make('intereses_generados')
                            ->default(0)
                            ->dehydrateStateUsing(function ($get) {
                                $valorBase = $get('valor') ?? 0;
                                $tasaEA = $get('tasa_ea') ?? 0;
                                $plazo = $get('plazo') ?? 0;

                                if ($valorBase > 0 && $tasaEA > 0 && $plazo > 0) {
                                    $tasaEA = $tasaEA / 100;
                                    $tasaDiaria = pow(1 + $tasaEA, 1 / 365) - 1;
                                    $intereses = $valorBase * $tasaDiaria * $plazo;
                                    return round($intereses, 2);
                                }
                                return 0;
                            }),

                        Forms\Components\Hidden::make('valor_retencion')
                            ->default(0)
                            ->dehydrateStateUsing(function ($get) {
                                $retencion = $get('retencion') ?? 0;
                                $valorBase = $get('valor') ?? 0;
                                $tasaEA = $get('tasa_ea') ?? 0;
                                $plazo = $get('plazo') ?? 0;
                                $interesesCalculados = 0;

                                if ($valorBase > 0 && $tasaEA > 0 && $plazo > 0) {
                                    $tasaEA = $tasaEA / 100;
                                    $tasaDiaria = pow(1 + $tasaEA, 1 / 365) - 1;
                                    $interesesCalculados = $valorBase * $tasaDiaria * $plazo;
                                }

                                if ($interesesCalculados > 0 && $retencion > 0) {
                                    $valorRet = $interesesCalculados * ($retencion / 100);
                                    return round($valorRet, 2);
                                }
                                return 0;
                            }),
                    ])
                    ->columns(5),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('numero_cdat')
            ->modifyQueryUsing(fn(Builder $query) => $query->whereIn('estado', ['A', 'P']))
            ->columns([
                Tables\Columns\TextColumn::make('numero_cdat')
                    ->label('CDT')
                    ->alignment(Alignment::Center),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->formatStateUsing(fn($state) => match ($state) {
                        'A' => 'Activo - Vigente',
                        'P' => 'Solicitado',
                        default => $state,
                    })
                    ->colors([
                        'primary' => 'A',
                        'warning' => 'P',
                    ])
                    ->alignment(Alignment::Center),

                Tables\Columns\TextColumn::make('plazo')
                    ->label('Plazo')
                    ->alignment(Alignment::Center),

                ColumnGroup::make('Fechas Titulo', [
                    Tables\Columns\TextColumn::make('fecha_creacion')->label('Creación')->date('d/m/Y')->alignment(Alignment::Center),
                    Tables\Columns\TextColumn::make('fecha_vencimiento')->label('Vencimiento')->date('d/m/Y')->alignment(Alignment::Center),
                ])->alignment(Alignment::Center),

                Tables\Columns\TextColumn::make('tasa_ea')
                    ->label('Tasa EA')
                    ->suffix('%')
                    ->alignment(Alignment::Center),

                ColumnGroup::make('Valores del Titulo', [
                    Tables\Columns\TextColumn::make('valor')->label('Capital Inicial')->alignment(Alignment::End)->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                    Tables\Columns\TextColumn::make('intereses_generados')->label('Intereses')->alignment(Alignment::End)->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                    Tables\Columns\TextColumn::make('valor_retencion')->label('Retencion')->alignment(Alignment::End)->formatStateUsing(fn($state) => '$' . number_format($state, 0, ',', '.')),
                ])->alignment(Alignment::Center),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                ActionsTable::make('actualizar_datos_asociado')
                    ->label('1. Actualizar Datos del Asociado')
                    ->fillForm(function (): array {
                        $ownerRecord = $this->getOwnerRecord();
                        if (!$ownerRecord || !$ownerRecord->codigo_interno_pag) {
                            Notification::make()->warning()->title('Faltan Datos')->body('El asociado no tiene un código interno de pago asignado.')->send();
                            return [];
                        }

                        $tercero = Tercero::where('tercero_id', $ownerRecord->codigo_interno_pag)->first();
                        if (!$tercero) {
                            Notification::make()->danger()->title('Error')->body('No se encontró al asociado con el código interno: ' . $ownerRecord->codigo_interno_pag)->send();
                            return [];
                        }

                        return [
                            'nro_identificacion' => $tercero->tercero_id,
                            'nombres' => $tercero->nombres,
                            'primer_apellido' => $tercero->primer_apellido,
                            'segundo_apellido' => $tercero->segundo_apellido,
                            'tipo_documento_id' => $tercero->tipo_identificacion_id,
                            'ocupacion' => $tercero->profesion_id,
                            'direccion' => $tercero->direccion,
                            'barrio' => $tercero->barrio_id,
                            'ciudad' => $tercero->ciudad_id,
                            'nro_celular_1' => $tercero->celular,
                            'nro_telefono_fijo' => $tercero->telefono,
                            'correo' => $tercero->email,
                            'total_activos' => $tercero->InformacionFinanciera->total_activos ?? null,
                            'total_pasivos' => $tercero->InformacionFinanciera->total_pasivos ?? null,
                            'salario' => $tercero->InformacionFinanciera->salario ?? null,
                            'servicios' => $tercero->InformacionFinanciera->servicios ?? null,
                            'otros_ingresos' => $tercero->InformacionFinanciera->otros_ingresos ?? null,
                            'gastos_sostenimiento' => $tercero->InformacionFinanciera->gastos_sostenimiento ?? null,
                            'gastos_financieros' => $tercero->InformacionFinanciera->gastos_financieros ?? null,
                            'arriendos' => $tercero->InformacionFinanciera->arriendos ?? null,
                            'gastos_personales' => $tercero->InformacionFinanciera->gastos_personales ?? null,
                            'otros_gastos' => $tercero->InformacionFinanciera->otros_gastos ?? null,


                        ];
                    })
                    ->form([
                        Section::make('Actualización de Datos Personales')
                            ->schema([
                                Forms\Components\TextInput::make('nro_identificacion')->label('Nro Identificación')->columns(2)->disabled(),
                                Forms\Components\TextInput::make('nombres')->label('Nombre(s)')->required()->columns(1),
                                Forms\Components\TextInput::make('primer_apellido')->label('Primer Apellido')->required(),
                                Forms\Components\TextInput::make('segundo_apellido')->label('Segundo Apellido')->nullable(),
                                Forms\Components\Select::make('tipo_documento_id')->label('Tipo de Documento')->required()->options(TipoIdentificacion::all()->pluck('nombre', 'id'))->searchable(),
                                Forms\Components\TextInput::make('direccion')->label('Dirección')->required(),
                                Forms\Components\Select::make('barrio')->label('Barrio')->required()->options(Barrio::all()->pluck('nombre', 'id'))->searchable(),
                                Forms\Components\Select::make('ciudad')->label('Ciudad')->required()->options(Ciudad::all()->pluck('nombre', 'id'))->searchable(),
                                Forms\Components\TextInput::make('nro_celular_1')->label('Nro Celular 1')->required(),
                                Forms\Components\TextInput::make('nro_telefono_fijo')->label('Teléfono Fijo')->required(),
                                Forms\Components\TextInput::make('correo')->label('Correo Electrónico')->required()->email(),
                            ])->columns(3),
                        Section::make('Actualización de Datos Financieros')
                            ->schema([
                                TextInput::make('total_activos')
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(5)
                                    ->minValue(0)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->live(onBlur: true)
                                    ->label('Total Activos'),
                                TextInput::make('total_pasivos')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(5)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->label('Total Pasivos'),
                                TextInput::make('salario')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(3)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->label('Ingresos por Salario / Pensión'),
                                TextInput::make('servicios')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(3)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->label('Ingresos por Servicios'),

                                TextInput::make('otros_ingresos')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(3)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->live(onBlur: true)
                                    ->minValue(0)
                                    ->label('Otros Ingresos'),

                                TextInput::make('arriendos')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->inputMode('decimal')
                                    ->columnSpan(2)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->label('G. Arriendo / Vivienda'),
                                TextInput::make('gastos_personales')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->inputMode('decimal')
                                    ->columnSpan(2)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->prefix('$')
                                    ->minValue(0)
                                    ->label('Gastos Personales'),
                                TextInput::make('gastos_sostenimiento')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->live(onBlur: true)
                                    ->columnSpan(2)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->minValue(0)
                                    ->label('Gastos Sostenimiento'),
                                TextInput::make('gastos_financieros')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(2)
                                    ->minValue(0)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->label('Gastos Financieros'),
                                TextInput::make('otros_gastos')
                                    ->prefix('$')
                                    ->numeric()
                                    ->required()
                                    ->live(onBlur: true)
                                    ->autocomplete(false)
                                    ->markAsRequired(false)
                                    ->maxLength(15)
                                    ->columnSpan(2)
                                    ->inputMode('decimal')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->minValue(0)
                                    ->label('Otros Gastos'),


                            ])->columns(10),
                    ])
                    ->action(function (array $data): void {
                        $ownerRecord = $this->getOwnerRecord();
                        if (!$ownerRecord || !$ownerRecord->codigo_interno_pag) {
                            return;
                        }

                        $tercero = Tercero::where('tercero_id', $ownerRecord->codigo_interno_pag)->first();
                        if (!$tercero) {
                            return;
                        }

                        $tercero->update([
                            'nombres' => $data['nombres'],
                            'primer_apellido' => $data['primer_apellido'],
                            'segundo_apellido' => $data['segundo_apellido'],
                            'tipo_identificacion_id' => $data['tipo_documento_id'],
                            'direccion' => $data['direccion'],
                            'barrio_id' => $data['barrio'],
                            'ciudad_id' => $data['ciudad'],
                            'celular' => $data['nro_celular_1'],
                            'telefono' => $data['nro_telefono_fijo'],
                            'email' => $data['correo'],
                        ]);

                        $tercero->InformacionFinanciera()->updateOrCreate(
                            ['tercero_id' => $tercero->id],
                            [
                                'total_activos' => $data['total_activos'],
                                'total_pasivos' => $data['total_pasivos'],
                                'salario' => $data['salario'],
                                'otros_ingresos' => $data['otros_ingresos'],
                                'gastos_sostenimiento' => $data['gastos_sostenimiento'],
                                'gastos_financieros' => $data['gastos_financieros'],
                                'gastos_personales' => $data['gastos_personales'],
                                'otros_gastos' => $data['otros_gastos'],
                                'servicios' => $data['servicios'],
                                'arriendos' => $data['arriendos'],
                            ]
                        );

                        Notification::make()
                            ->title('Datos del asociado actualizados')
                            ->success()
                            ->send();
                        $this->ownerDataUpdated = true;
                    })
                    ->slideOver()
                    ->modalSubmitActionLabel('Guardar Actualización'),


                Tables\Actions\CreateAction::make()
                    ->label('2. Registrar CDAT')
                    ->slideOver()
                    ->createAnother(false)
                    ->color('info') // Cambia el color del botón a verde
                    ->modalSubmitActionLabel('Registrar CDAT')
                    ->disabled(fn() => !$this->ownerDataUpdated)
                    ->tooltip(fn() => $this->ownerDataUpdated ? 'Crear un nuevo CDAT para el asociado' : 'Primero debe actualizar los datos del asociado (Paso 1).')
                    ->after(function ($record) {
                        $docContable = TipoDocumentoContable::where('sigla', 'CDT')->first();
                        if ($docContable) {
                            $docContable->numerador = $record->numero_cdat;
                            $docContable->save();
                        }
                    }),
            ])
            ->actions([
                ActionsTable::make('imprimir_formato_2')
                    ->label('Bienvenida')
                    ->icon('heroicon-o-printer')
                    ->color('emerald')
                    ->iconSize('lg')
                    ->url(fn($record) => route('cdat-carta.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->estado !== 'P'),
                ActionsTable::make('imprimir_formato')
                    ->label('Imprimir Título')
                    ->icon('heroicon-o-hand-thumb-up')
                    ->color('success')
                    ->iconSize('lg')
                    ->url(fn($record) => route('cdat.print', $record))
                    ->openUrlInNewTab()
                    ->visible(fn($record) => $record->estado !== 'P'),

            ])
            ->bulkActions([
                //
            ]);
    }
}
