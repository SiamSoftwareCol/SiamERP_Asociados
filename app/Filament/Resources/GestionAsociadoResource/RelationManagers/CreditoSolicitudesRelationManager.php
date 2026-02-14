<?php

namespace App\Filament\Resources\GestionAsociadoResource\RelationManagers;

use App\Models\Barrio;
use App\Models\CarteraEncabezado;
use App\Models\Ciudad;
use App\Models\CreditoLinea;
use App\Models\CreditoSolicitud;
use App\Models\Asesor;
use App\Models\Pagaduria;
use App\Models\CuotaEncabezado;
use App\Models\Garantia;
use App\Models\PlanDesembolso;
use App\Models\Tasa;
use App\Models\Tercero;
use App\Models\TipoIdentificacion;
use Filament\Forms;
use Filament\Tables\Actions\Action as ActionsTable;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Set;
use Filament\Forms\Get;

class CreditoSolicitudesRelationManager extends RelationManager
{
    protected static string $relationship = 'creditoSolicitudes';
    public bool $ownerDataUpdated = false;

    public function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('observaciones')
            ->columns([
                Tables\Columns\TextColumn::make('solicitud')->label('Nro solicitud')->default('N/A'),
                Tables\Columns\TextColumn::make('estado')->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'P' => 'success',
                        'N' => 'danger',
                        'M' => 'gray',
                        'A' => 'primary',
                        'C' => 'warning',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'P' => 'PENDIENTE',
                        'N' => 'NEGADA',
                        'M' => 'MONTO DESEMBOLSADO',
                        'A' => 'APROBADA',
                        'C' => 'CANCELADA',
                    }),
                Tables\Columns\TextColumn::make('linea')->label('Linea de credito')->default('N/A'),
                Tables\Columns\TextColumn::make('tasa_id')->label('Interes Corriente')->formatStateUsing(fn($state) => $state !== null ? number_format($state, 2) . ' %' : 'N/A')->default('N/A'),
                Tables\Columns\TextColumn::make('nro_cuotas_max')->label('Nro Cuotas')->default('N/A'),
                Tables\Columns\TextColumn::make('fecha_solicitud')->label('Fecha Solicitud')->default('N/A'),
            ])
            ->filters([])
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

                ActionsTable::make('create_solicitud_credito')->label('2. Crear Solicitud de crédito')
                    ->color('info')
                    ->modalSubmitActionLabel('Crear Solicitud')
                    ->disabled(fn() => !$this->ownerDataUpdated)
                    ->tooltip(fn() => $this->ownerDataUpdated ? 'Crear una nueva solicitud para el asociado' : 'Primero debe actualizar los datos del asociado (Paso 1).')
                    ->form([
                        Section::make()
                            ->schema([
                                Forms\Components\Select::make('linea')
                                    ->label('Linea de credito')
                                    ->searchable()
                                    ->options(
                                        CreditoLinea::query()
                                            ->select('id', DB::raw("CONCAT(linea, ' - ', descripcion) as display_name"))
                                            ->orderBy('display_name', 'asc')
                                            ->pluck('display_name', 'id')
                                    )
                                    ->live()
                                    ->required(),
                                Forms\Components\Select::make('empresa')
                                    ->label('Pagaduria')
                                    ->required()
                                    ->searchable()
                                    ->options(
                                        Pagaduria::query()
                                            ->where('estado', true)
                                            ->select('id', DB::raw("nombre as display_name"))
                                            ->pluck('display_name', 'id')
                                    ),
                                Forms\Components\Select::make('tipo_desembolso')
                                    ->options([
                                        'V' => 'Pago Directo (Ventanilla)',
                                        'N' => 'Descuento de Nómina (Libranza)'
                                    ])
                                    ->label('Tipo de desembolso')
                                    ->native(false),
                                Forms\Components\TextInput::make('vlr_solicitud')
                                    ->label('Valor Solicitud')
                                    ->numeric()
                                    ->autocomplete(false)
                                    ->minValue(0)
                                    ->maxValue(fn(Get $get): int => CreditoLinea::find($get('linea'))->monto_max ?? 0)
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->inputMode('decimal')
                                    ->prefix('$')
                                    ->validationMessages([
                                        'max' => 'El :attribute no puede ser mayor al permitido.',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('nro_cuotas_max')
                                    ->label('No de Cuotas')
                                    ->numeric()
                                    ->required()
                                    ->autocomplete(false)
                                    ->minValue(0)
                                    ->maxValue(fn(Get $get): int => CreditoLinea::find($get('linea'))->nro_cuotas_max ?? 0)
                                    ->validationMessages([
                                        'max' => 'El :attribute no puede ser mayor al permitido.',
                                    ])
                                    ->helperText('Plazo maximo de pago'),
                                Forms\Components\DatePicker::make('fecha_primer_vto')
                                    ->label('Fecha cuota 1')
                                    ->required()
                                    ->native(false)
                                    ->displayFormat('d/m/Y')
                                    ->minDate(now())
                                    ->helperText('Fecha de la primera cuota'),
                                Forms\Components\Select::make('tasa_id')
                                    ->label('Tasa interes')
                                    ->required()
                                    ->options(fn() => Tasa::query()->orderBy('tasa', 'asc')->pluck('nombre', 'id'))
                                    ->native(false),
                                Forms\Components\Select::make('tercero_asesor')
                                    ->label('Codigo Asesor')
                                    ->options(fn() => Asesor::query()
                                        ->select(DB::raw("id, CONCAT(codigo_asesor, ' - ', nombre) AS nombre_completo"))
                                        ->pluck('nombre_completo', 'id'))
                                    ->required(),
                                Forms\Components\Textarea::make('observaciones')
                                    ->label('Observaciones')
                                    ->required()
                                    ->autocomplete(false)
                                    ->maxLength(255)
                                    ->columnSpanFull(),
                            ])->columns(2)
                    ])
                    ->action(function (array $data): void {

                        DB::transaction(function () use ($data) {
                            $tasa = Tasa::find($data['tasa_id']);
                            $data['tasa_float'] = floatval($tasa->tasa);
                            $linea = CreditoLinea::find($data['linea']);
                            $fechaFinalCalculada = \Carbon\Carbon::parse($data['fecha_primer_vto'])
                                ->addMonthsNoOverflow($data['nro_cuotas_max'] - 1)
                                ->format('Y-m-d');

                            // =================================================================
                            // 1. CREDITO SOLICITUDES
                            // =================================================================
                            $credito = CreditoSolicitud::create([
                                'asociado_id'       => $this->getOwnerRecord()->id,
                                'linea'             => $data['linea'],
                                'empresa'           => $data['empresa'],
                                'asociado'          => $this->getOwnerRecord()->codigo_interno_pag,
                                'tipo_desembolso'   => $data['tipo_desembolso'],
                                'periodo_pago'      => 1,
                                'moneda'            => 1,
                                'vlr_solicitud'     => $data['vlr_solicitud'],
                                'vlr_planes'        => $data['vlr_solicitud'],
                                'nro_cuotas_max'    => $data['nro_cuotas_max'],
                                'fecha_primer_vto'  => $data['fecha_primer_vto'],
                                'tasa_id'           => $linea->interes_cte,
                                'interes_mora'      => $linea->interes_mora,
                                'tipo_cuota'        => $linea->tipo_cuota,
                                'tasa_incremento'   => 0,
                                'abonos_extra'      => $linea->abonos_extra,
                                'tercero_asesor'    => $data['tercero_asesor'],
                                'observaciones'     => $data['observaciones'],
                                'estado'            => 'P',
                                'reestructuracion'  => 'N',
                                'vlr_cuota'         => 0,
                                'fecha_solicitud'   => now()->format('Y-m-d'),
                                'fecha_novedad'     => now()->format('Y-m-d'),
                                'int_cte_mlv'       => 'N',
                                'int_mora_mlv'      => 'N',
                                'lista_desembolso'  => 'N',
                                'usuario_crea'      => auth()->user()->name
                            ]);

                            // =================================================================
                            // 2. PLAN DESEMBOLSOS
                            // =================================================================
                            $plan_desembolso = PlanDesembolso::create([
                                'solicitud_id'          => $credito->solicitud,
                                'plan_numero'           => 1,
                                'fecha_plan'            => now()->format('Y-m-d'),
                                'fecha_inicio'          => $credito->fecha_primer_vto,
                                'valor_plan'            => $credito->vlr_solicitud,
                                'modo_desembolso'       => null,
                                'tipo_documento_enc'    => 'PLI',
                                'nro_documento_vto_enc' => 0,
                            ]);

                            // =================================================================
                            // 3. CARTERA ENCABEZADOS (Preliquidación - PLI)
                            // =================================================================
                            $cartera_encabezado = CarteraEncabezado::create([
                                'tdocto'                => 'PLI',
                                'cliente'               => $this->getOwnerRecord()->codigo_interno_pag,
                                'linea'                 => $data['linea'],
                                'estado'                => 'A',
                                'periodo_pago'          => 1,
                                'moneda'                => 1,
                                'interes_cte'           => $data['tasa_float'],
                                'interes_mora'          => $linea->interes_mora ?? 0,
                                'tipo_cuota'            => $linea->tipo_cuota,
                                'forma_pago_int'        => 'V',
                                'forma_descuento'       => $data['tipo_desembolso'],
                                'tipo_tasa'             => $linea->tipo_tasa,
                                'nro_cuotas_gracia'     => 0,
                                'abonos_extra'          => $linea->abonos_extra,
                                'extra_periodico'       => 'N',
                                'periodo_abono'         => 0,
                                'vlr_abono'             => 0.00,
                                'fecha_docto'           => now()->format('Y-m-d'),
                                'fecha_primer_vto'      => $data['fecha_primer_vto'],
                                'vlr_docto_vto'         => $data['vlr_solicitud'],
                                'vlr_ini_cuota'         => 0.00,
                                'fecha_desembolso'      => null,
                                'vlr_desembolsado'      => 0.00,
                                'fecha_ult_pago_cte'    => null,
                                'fecha_ult_pago_mora'   => null,
                                'vlr_saldo_actual'      => $data['vlr_solicitud'],
                                'fecha_pago_total'      => $fechaFinalCalculada,
                                'ult_categoria'         => 'A',
                                'usuario_crea'          => auth()->user()->name,
                                'tdocto_cancel'         => 'DPA',
                                'nro_docto_cancel'      => $credito->id,
                                'categoria_actual'      => 'A',
                                'vlr_abono_rec'         => 0.00,
                                'vlr_abono_ncr'         => 0.00,
                                'vlr_abono_dpa'         => 0.00,
                                'nro_cuotas'            => 0,
                                'nro_dias_mora'         => 0,
                                'tdocto_cancel'         => 'DPA',
                                'vlr_reliquidado'       => 0.00,
                                'nro_docto_anterior'    => null,
                                'vlr_congelada'         => 0.00,
                                'vlr_provision_acum'    => 0.00,
                                'vlr_ult_provision'     => 0.00,
                                'vlr_cuentas_orden'     => 0.00,
                                'vlr_causado'           => 0.00,
                                'docto_cargado'         => 'N',
                                'vlr_provision'         => 0.00,
                                'vlr_causacion_mes'     => 0.00,
                                'vlr_cuentas_orden_mes' => 0.00,
                                'tdocto_desembolso'     => null,
                                'nro_docto_desembolso'  => 0,
                                'vlr_cuota_tabla'       => 0.00,
                                'empresa'               => $data['empresa'],
                                'nro_cuotas_iniciales'  => $data['nro_cuotas_max'],
                                'tercero_asesor'        => $data['tercero_asesor']
                            ]);


                            $plan_desembolso->update([
                                'nro_documento_vto_enc' => $cartera_encabezado->nro_docto
                            ]);

                            $credito_lineas_conceptos = DB::table('credito_lineas_conceptos')->where('linea_id', $credito->linea)->get()->toArray();
                            foreach ($credito_lineas_conceptos as $cartera_composicion_conceptos) {
                                DB::table('cartera_composicion_conceptos')->insert([
                                    'tipo_documento'       => 'PLI',
                                    'numero_documento'     => $cartera_encabezado->nro_docto,
                                    'concepto_descuento'   => $cartera_composicion_conceptos->codigo_descuento,
                                    'prioridad'            => $cartera_composicion_conceptos->prioridad,
                                    'valor'                => $cartera_composicion_conceptos->valor,
                                    'valor_con_descuento'  => $cartera_composicion_conceptos->valor_descuento,
                                    'porcentaje_descuento' => $cartera_composicion_conceptos->porcentaje_descuento,
                                    'comodin'              => $cartera_composicion_conceptos->comodin
                                ]);
                            }

                            // Amortización
                            $cuotas = calcular_amortizacion($data['vlr_solicitud'], $data['tasa_float'], $data['nro_cuotas_max']);
                            $vlr_ini_cuota = 0.00;
                            $nro_cuotas = 0;
                            $cuotas_encabezados = array();
                            $fechaOriginal = ($data['fecha_primer_vto']) ? \Carbon\Carbon::parse($data['fecha_primer_vto']) : now();

                            // =================================================================
                            // 4. CUOTAS ENCABEZADOS (Corregido: sin amortizacion_capital en BD)
                            // =================================================================
                            foreach ($cuotas as $index => $cuota) {
                                $fechaVencimiento = $fechaOriginal->copy()->addMonthsNoOverflow($index);

                                $nuevo_encabezado = CuotaEncabezado::create([
                                    'tdocto'            => 'PLI',
                                    'nro_docto'         => $cartera_encabezado->nro_docto,
                                    'nro_cuota'         => $cuota['periodo'],
                                    'interes_cte'       => $cuota['interes'],
                                    'vlr_cuota'         => $cuota['pago'],
                                    'saldo_capital'     => $cuota['saldo'],
                                    'consecutivo'       => 1,
                                    'estado'            => 'A',
                                    'iden_cuota'        => 'N',
                                    'interes_mora'      => 0,
                                    'fecha_vencimiento' => $fechaVencimiento->format('Y-m-d'),
                                    'fecha_pago_total'  => null,
                                    'dias_mora'         => 0,
                                    'vlr_abono_rec'     => 0.00,
                                    'vlr_abono_ncr'     => 0.00,
                                    'vlr_abono_dpa'     => 0.00,
                                    'vlr_descuento'     => 0.00,
                                    'forma_descuento'   => $cartera_encabezado->forma_descuento,
                                    'vlr_cuentas_orden' => 0.00,
                                    'vlr_causado'       => 0.00,
                                ]);

                                // Asignar propiedades temporales en el objeto PHP para el siguiente paso
                                $nuevo_encabezado->amortizacion_capital = $cuota['amortizacion_capital'];
                                $nuevo_encabezado->interes_calculado = $cuota['interes'];

                                array_push($cuotas_encabezados, $nuevo_encabezado);
                                $vlr_ini_cuota = $cuota['pago'];
                                $nro_cuotas = $cuota['periodo'];
                            }

                            // =================================================================
                            // 5. CUOTAS DETALLES
                            // =================================================================
                            $conceptos_creditos = DB::table('cartera_composicion_conceptos')
                                ->where('numero_documento', $cartera_encabezado->nro_docto)
                                ->get()
                                ->toArray();

                            foreach ($cuotas_encabezados as $cuota) {
                                foreach ($conceptos_creditos as $concepto) {
                                    $valorDetalle = 0.00;


                                    if ($concepto->concepto_descuento == 1) {
                                        $valorDetalle = $cuota->amortizacion_capital;
                                    } elseif ($concepto->concepto_descuento == 2) {
                                        $valorDetalle = $cuota->interes_calculado;
                                    }

                                    DB::table('cuotas_detalles')->insert([
                                        'tdocto'        => 'PLI',
                                        'nro_docto'     => $cartera_encabezado->nro_docto,
                                        'nro_cuota'     => $cuota->nro_cuota,
                                        'consecutivo'   => 1,
                                        'estado'        => 'A',
                                        'vlr_detalle'   => $valorDetalle,
                                        'con_descuento' => $concepto->concepto_descuento
                                    ]);
                                }
                            }

                            $cartera_encabezado->update([
                                'vlr_ini_cuota'   => $vlr_ini_cuota,
                                'nro_cuotas'      => $nro_cuotas,
                                'vlr_cuota_tabla' => $vlr_ini_cuota
                            ]);

                            $this->dispatch('download', [[$this->getOwnerRecord(), $credito]]);

                            Notification::make()
                                ->title('Se crearon los datos correctamente')
                                ->icon('heroicon-m-check-circle')
                                ->body('Solicitud y Preliquidación (PLI) generadas exitosamente.')
                                ->success()
                                ->send();
                        }, 5);
                    })
            ])


            ->actions([

                Tables\Actions\Action::make('Garantias')
                    ->label('+ Garantías')
                    ->icon('heroicon-o-shield-check')
                    ->color('primary')
                    ->visible(fn($record) => $record->estado === 'P')
                    ->form([
                        Forms\Components\TextInput::make('tipo_garantia')
                            ->label('Tipo de garantía')
                            ->required(),

                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3),

                        Forms\Components\TextInput::make('valor')
                            ->label('Valor')
                            ->numeric()
                            ->required(),
                    ])
                    ->action(function (array $data, $record): void {
                        $record->garantias()->create([
                            'tipo_garantia' => $data['tipo_garantia'],
                            'descripcion'   => $data['descripcion'],
                            'valor'         => $data['valor'],
                        ]);

                        Notification::make()
                            ->title('Garantía registrada')
                            ->success()
                            ->body('La garantía ha sido asociada correctamente a la solicitud.')
                            ->send();
                    }),
                Tables\Actions\Action::make('Preliquidacion')
                    ->label('Ver Preliquidación')
                    ->icon('heroicon-o-document-text')
                    ->color('info')
                    ->visible(fn($record) => $record->estado === 'P')
                    ->modalHeading('Preliquidación del Crédito')
                    ->modalWidth('3xl')
                    ->modalContent(function ($record) {
                        $tdocto = 'PLI';
                        $nro_docto = $record->nro_docto;

                        $cartera = DB::table('cartera_encabezados')
                            ->where('tdocto', $tdocto)
                            ->where('nro_docto', $nro_docto)
                            ->first();

                        if (!$cartera) {
                            $cartera = DB::table('cartera_encabezados')
                                ->where('tdocto', $tdocto)
                                ->where('cliente', $record->asociado)
                                ->orderBy('id', 'desc')
                                ->first();
                        }
                        $detalles = DB::table('cuotas_detalles')
                            ->where('tdocto', $tdocto)
                            ->where('nro_docto', $cartera->nro_docto)
                            ->get();
                        $codigosConceptos = $detalles->pluck('con_descuento')->unique();
                        $nombresConceptos = DB::table('concepto_descuentos')
                            ->whereIn('codigo_descuento', $codigosConceptos)
                            ->pluck('descripcion', 'codigo_descuento');
                        $detallesAgrupados = [];
                        foreach ($detalles as $det) {
                            $nombre = $nombresConceptos[$det->con_descuento] ?? $det->con_descuento;
                            $detallesAgrupados[$det->nro_cuota][$nombre] = $det->vlr_detalle;
                        }
                        $conceptos = collect($detallesAgrupados)->flatMap(function ($cuota) {
                            return array_keys($cuota);
                        })->unique()->values();

                        return view('filament.modals.preliquidacion', [
                            'cartera' => $cartera,
                            'cuotas' => DB::table('cuotas_encabezados')
                                ->where('tdocto', $tdocto)
                                ->where('nro_docto', $cartera->nro_docto)
                                ->orderBy('nro_cuota', 'asc')
                                ->get(),
                            'conceptos' => $conceptos,
                            'detallesAgrupados' => $detallesAgrupados,
                        ]);
                    }),
                Tables\Actions\Action::make('descargar_pdf')
                    ->label('Imprimir Solicitud')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->visible(fn($record) => $record->estado === 'P')
                    ->action(function ($record) {
                        $owner = $this->getOwnerRecord();
                        $tercero = Tercero::where('tercero_id', $owner?->codigo_interno_pag)->first();
                        $finanzas = $tercero?->InformacionFinanciera;
                        $credito = $record->fresh();
                        $pdf = Pdf::loadView('pdf.solicitud_credito', [
                            'credito'  => $credito,
                            'asociado' => $owner,
                            'tercero'  => $tercero,
                            'finanzas' => $finanzas,
                        ])->setPaper('A4', 'portrait');

                        return response()->streamDownload(
                            fn() => print($pdf->output()),
                            'Solicitud_' . ($credito->solicitud ?? $credito->id) . '.pdf'
                        );
                    }),
                Tables\Actions\ViewAction::make('verSolicitud')
                    ->label('Ver Solicitud')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('Detalle de la Solicitud de Crédito')
                    ->modalWidth('5xl')
                    ->modalContent(function ($record) {
                        if (! $record) {
                            return '<div class="text-red-600 font-semibold">No se encontró la información de la solicitud.</div>';
                        }
                        return view('filament.modals.credito-solicitud-general', [
                            'solicitud' => $record,
                        ]);
                    }),
            ])

            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([]),
            ])
            ->deferLoading()
            ->defaultSort('id', 'desc');
    }
}

function calcular_amortizacion($principal, $tasa_anual, $plazo_meses)
{
    $tasa_mensual = $tasa_anual / 12 / 100;
    $pago_mensual = $principal * $tasa_mensual * pow((1 + $tasa_mensual), $plazo_meses) / (pow((1 + $tasa_mensual), $plazo_meses) - 1);

    $saldo = $principal;
    $tabla_amortizacion = array();

    for ($periodo = 1; $periodo <= $plazo_meses; $periodo++) {
        $interes = $saldo * $tasa_mensual;
        $amortizacion_capital = $pago_mensual - $interes;
        $saldo -= $amortizacion_capital;

        array_push($tabla_amortizacion, array(
            'periodo' => $periodo,
            'pago' => round($pago_mensual, 2),
            'interes' => round($interes, 2),
            'amortizacion_capital' => round($amortizacion_capital, 2),
            'saldo' => round($saldo, 2)
        ));
    }

    return $tabla_amortizacion;
}

function showModal(): bool
{
    return false;
    sleep(5);
    return true;
}
