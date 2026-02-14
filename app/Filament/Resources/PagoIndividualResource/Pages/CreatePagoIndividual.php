<?php

namespace App\Filament\Resources\PagoIndividualResource\Pages;

use App\Filament\Resources\PagoIndividualResource;
use App\Models\Tercero;
use App\Models\TipoDocumentoContable;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CreatePagoIndividual extends CreateRecord
{
    protected static string $resource = PagoIndividualResource::class;
    protected static string $view = 'custom.tesoreria.create-pagos-individual';
    protected static ?string $pollingInterval = null;

    public bool $show = false;
    public $cliente;
    public $concepto_descuento;
    public float $efectivo = 0;
    public float $cheque = 0;
    public float $valor_abonar = 0;
    public float $aplica_valor_a_total = 0;
    public $tipo_documento_id = null;
    public $cuentaCapital;
    public $tipo_pago = null;
    public float $pendiente = 0;
    public $nro_docto_actual;
    public float $total_a_pagar = 0;
    public $credito_aplicados;
    public $sigla_documento;
    public $numerador;
    public $fecha_proceso;

    public function mount(): void
    {
        // Inicializamos la fecha al cargar la página
        $this->fecha_proceso = now()->format('Y-m-d');

        // Llamada al mount padre si es necesario, aunque en CreateRecord suele ser handleRecordCreation
        parent::mount();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('tipo_documento')
                    ->label('Tipo de documento')
                    ->options(TipoDocumentoContable::query()
                        ->where('uso_pago', true)
                        ->select(DB::raw("id, CONCAT(sigla, ' - ', tipo_documento) AS nombre_completo"))
                        ->pluck('nombre_completo', 'id'))
                    ->live()
                    ->columnSpan(1)
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $this->tipo_documento_id = $state;
                        if ($state) {
                            $numerador = TipoDocumentoContable::find($state);
                            if ($numerador) {
                                $this->sigla_documento = $numerador->sigla;
                                $this->numerador = $numerador->numerador;
                                $set('nro_documento', $numerador->numerador);
                            }
                        }
                    }),

                TextInput::make('nro_documento')
                    ->label('Nro de documento')
                    ->disabled(),

                Select::make('tipo_pago')
                    ->label('Tipo de pago')
                    ->options(DB::table('concepto_descuentos')
                        ->where('identificador_concepto', 'PG')
                        ->get()
                        ->pluck('descripcion', 'id'))
                    ->live(onBlur: true)
                    ->columnSpan(1)
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state) {
                        if ($state) {
                            $concepto = DB::table('concepto_descuentos')->find($state);
                            $this->tipo_pago = $concepto?->cuenta_contable;
                        }
                    }),

                // AJUSTE: Cambiado de TextInput a DatePicker para mejor control
                DatePicker::make('fecha_proceso')
                    ->label('Fecha de Proceso')
                    ->prefixIcon('heroicon-c-calendar-days')
                    ->default(now())
                    ->displayFormat('d/m/Y')
                    ->format('Y-m-d')
                    ->native(false) // Se ve más bonito con el calendario de Filament
                    ->closeOnDateSelection()
                    ->required()
                    ->live() // Por si necesitas recalcular intereses basados en fecha futura
                    ->afterStateUpdated(fn ($state) => $this->fecha_proceso = $state),

                // -------------------------------------------------------------
                // CAMPO CLIENTE: Lógica principal de búsqueda y cálculo
                // -------------------------------------------------------------
                TextInput::make('cliente')
                    ->live(onBlur: true)
                    ->placeholder('Nro identificación cliente')
                    ->prefixIcon('heroicon-c-magnifying-glass-circle')
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {
                        if (!$state) return;

                        $asociado = Tercero::where('tercero_id', $state)->first();

                        if ($asociado) {
                            $set('nombre', $asociado->nombres . ' ' . $asociado->primer_apellido . ' ' . $asociado->segundo_apellido);
                            $set('direccion', $asociado->direccion);
                            $set('telefono', $asociado->celular);

                            $this->cliente = $asociado;
                            $this->show = true;

                            $this->calcularDeudaTotal($asociado->tercero_id, $set);
                        } else {
                            Notification::make()->warning()->title('Cliente no encontrado')->send();
                        }
                    }),

                TextInput::make('nombre')
                    ->placeholder('Nombre del cliente')
                    ->prefixIcon('heroicon-c-user')
                    ->disabled(),

                TextInput::make('direccion')
                    ->placeholder('Dirección del cliente')
                    ->prefixIcon('heroicon-c-map')
                    ->disabled(),

                TextInput::make('telefono')
                    ->placeholder('Telefono del cliente')
                    ->disabled(),

                Section::make('Información de pago')
                    ->schema([
                        TextInput::make('efectivo')
                            ->placeholder('Monto efectivo')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalcularTotales($get, $set)),

                        TextInput::make('cheque')
                            ->placeholder('Nro cheque')
                            ->prefixIcon('heroicon-c-credit-card')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalcularTotales($get, $set)),

                        TextInput::make('valor_abonar')
                            ->placeholder('Valor a abonar')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->default(0)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (Get $get, Set $set) => $this->recalcularTotales($get, $set)),

                        // Campos de solo lectura (Calculados)
                        TextInput::make('valor_total')
                            ->placeholder('Valor total recibido')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->readOnly(),

                        TextInput::make('pendiente_por_aplicar')
                            ->placeholder('Pendiente por aplicar')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->readOnly()
                            ->extraInputAttributes(fn ($state) => ['class' => $state != 0 ? 'text-danger-600 font-bold' : 'text-success-600']),

                        TextInput::make('total_a_pagar')
                            ->placeholder('Total a pagar')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->readOnly(),
                    ])->columns(3)
            ])
            ->columns(4);
    }

    protected function calcularDeudaTotal($clienteId, Set $set)
    {
        $sumatoria = DB::table('cartera_encabezados')
            ->where('cliente', $clienteId)
            ->where('estado', 'A')
            ->where('tdocto', 'PAG')
            ->sum('vlr_congelada');

        $sumatoriaLiquidacion = DB::table('cartera_encabezados')
            ->where('cliente', $clienteId)
            ->where('estado', 'A')
            ->where('tdocto', 'PAG')
            ->sum('vlr_cuentas_orden');

        $sumatoriaObligaciones = DB::table('detalle_vencimiento_descuento')
            ->where('cliente', $clienteId)
            ->where('estado', 'A')
            ->sum('vlr_congelada');

        $otros_conceptos = DB::table('tmp_vencimiento_descuento')
            ->where('cliente', $clienteId)
            ->sum('valor');

        $total = $sumatoria + $sumatoriaLiquidacion + $sumatoriaObligaciones + $otros_conceptos + $this->aplica_valor_a_total;

        $this->total_a_pagar = (float) $total;
        $set('total_a_pagar', $total);

        $this->recalcularTotales(null, $set, true);
    }

    protected function recalcularTotales($get, Set $set, $init = false)
    {
        $e = ($init || !$get) ? 0 : (float) str_replace(',', '', $get('efectivo') ?? 0);
        $c = ($init || !$get) ? 0 : (float) str_replace(',', '', $get('cheque') ?? 0);
        $va = ($init || !$get) ? 0 : (float) str_replace(',', '', $get('valor_abonar') ?? 0);

        $this->efectivo = $e;
        $this->cheque = $c;
        $this->valor_abonar = $va;

        $totalRecibido = $e + $c + $va;
        $set('valor_total', $totalRecibido);

        $pendiente = $totalRecibido - $this->total_a_pagar;

        $this->pendiente = $pendiente;
        $set('pendiente_por_aplicar', $pendiente);
    }

    public function nroDoctoActual($nro_docto) { $this->nro_docto_actual = $nro_docto; }
    public function updateValorAplicado($nuevo_valor, $id) { DB::table('detalle_vencimiento_descuento')->where('id', $id)->update(['vlr_congelada' => $nuevo_valor]); }
    public function aplicarValor(int $nro_docto, float $valorAplicado) { DB::table('cartera_encabezados')->where('nro_docto', $nro_docto)->update(['vlr_congelada' => floatval($valorAplicado)]); }
    public function aplicarValorLiquidacion(int $nro_docto, float $valorAplicado) { DB::table('cartera_encabezados')->where('nro_docto', $nro_docto)->update(['vlr_cuentas_orden' => floatval($valorAplicado)]); }

    public function calcularIntereses(int $nro_docto)
    {
        // ... (Tu código actual de intereses va aquí sin cambios)
        if (!$nro_docto) { Notification::make()->title('Atención')->body('No se puede calcular interés')->warning()->send(); return; }
        $cuotasEncabezado = DB::table('cuotas_encabezados')->where('nro_docto', $nro_docto)->where('tdocto', 'PAG')->where('estado', 'C')->orderBy('fecha_vencimiento', 'desc')->get();
        $interes = 0;
        if ($cuotasEncabezado->isEmpty()) {
            $carteraEncabezado = DB::table('cartera_encabezados')->where('nro_docto', $nro_docto)->first();
            $primeraFechaVencimiento = Carbon::parse($carteraEncabezado->fecha_desembolso);
            $diasMora = $primeraFechaVencimiento->diffInDays(Carbon::parse(now()));
            $interes += $carteraEncabezado->vlr_saldo_actual * ($diasMora * $carteraEncabezado->interes_mora);
        } else {
            $primeraFechaVencimiento = Carbon::parse($cuotasEncabezado[0]->fecha_vencimiento);
            foreach ($cuotasEncabezado as $cuota) {
                $diasMora = $primeraFechaVencimiento->diffInDays(Carbon::parse(now()));
                $interes += $cuota->vlr_cuota * ($diasMora * $cuota->interes_mora);
            }
        }
        return ['primera_fecha' => $primeraFechaVencimiento, 'segunda_fecha' => now(), 'interes_mora' => number_format($interes, 2)];
    }

    public function generarLiquidacion(int $nro_docto)
    {
        // ... (Tu código actual de generarLiquidacion va aquí sin cambios)
        $limiteConfig = DB::table('general_settings')->value('more_configs->limite_cuotas_pagar');
        $limiteDinamico = $limiteConfig ? $limiteConfig * 4 : 20;
        return DB::table('cuotas_detalles as cds')->join('concepto_descuentos as cd', 'cds.con_descuento', '=', 'cd.codigo_descuento')->join('cartera_composicion_conceptos as ccc', function ($join) { $join->on('ccc.tipo_documento', '=', 'cds.tdocto')->on('ccc.numero_documento', '=', 'cds.nro_docto')->on('cds.con_descuento', '=', 'ccc.concepto_descuento'); })->where('cds.tdocto', 'PAG')->where('cds.nro_docto', $nro_docto)->where('cds.estado', 'A')->orderBy('cds.nro_cuota')->orderBy('ccc.prioridad')->select('cds.id', 'cds.nro_docto', 'cds.nro_cuota', 'cd.descripcion', 'ccc.prioridad', DB::raw('cds.vlr_detalle - (cds.vlr_abono_ncr + cds.vlr_abono_rec + cds.vlr_abono_dpa + cds.vlr_descuento) as vlr_detalle'), 'cds.vlr_cuentas_orden', 'cds.con_descuento')->limit($limiteDinamico)->get();
    }

    public function aplicaValorLiquidacion(int $nro_docto, array $cuotas)
    {
         foreach ($cuotas as $cuota) { DB::table('cuotas_detalles')->where('nro_docto', $nro_docto)->where('id', $cuota['id'])->update([ 'vlr_cuentas_orden' => isset($cuota['vlr_aplicar']) ? $cuota['vlr_aplicar'] : 0.00 ]); }
    }

    public function vencimientoDescuento($vencimiento)
    {
        // ... (Tu código actual)
        $existe = DB::table('tmp_vencimiento_descuento')->where('cliente', $vencimiento['cliente'])->where('puc', $vencimiento['cuenta_contable'])->where('codigo_concepto', $vencimiento['codigo_concepto'])->first();
        if ($existe) { DB::table('tmp_vencimiento_descuento')->where('id', $existe->id)->update(['valor' => $vencimiento['valor']]); return; }
        DB::table('tmp_vencimiento_descuento')->insert([ 'cliente' => $vencimiento['cliente'], 'puc' => $vencimiento['cuenta_contable'], 'valor' => $vencimiento['valor'], 'codigo_concepto' => $vencimiento['codigo_concepto'], 'descripcion' => $vencimiento['descripcion'], ]);
    }

    public function eliminaVencimiento($descuento) { DB::table('tmp_vencimiento_descuento')->where('id', $descuento)->delete(); }

    public function limpiarDatos()
    {
        // ... (Tu código actual)
        if ($this->cliente) {
            $clienteId = $this->cliente->tercero_id;
            DB::table('cartera_encabezados')->where('cliente', $clienteId)->where('estado', 'A')->where('tdocto', 'PAG')->update(['vlr_congelada' => 0.00, 'vlr_cuentas_orden' => 0.00]);
            DB::table('detalle_vencimiento_descuento')->where('cliente', $clienteId)->where('estado', 'A')->update(['vlr_congelada' => 0.00]);
            DB::table('tmp_vencimiento_descuento')->where('cliente', $clienteId)->delete();
            DB::table('cuotas_detalles')->join('cartera_encabezados as ce', 'ce.nro_docto', '=', 'cuotas_detalles.nro_docto')->where('ce.cliente', $clienteId)->where('ce.estado', 'A')->where('cuotas_detalles.tdocto', 'PAG')->update(['cuotas_detalles.vlr_cuentas_orden' => 0.00]);
        }
    }

    public function generarComprobante()
    {
        if (!$this->cliente) {
            Notification::make()->title('Atención')->body('Debe seleccionar un cliente')->warning()->send();
            return;
        }
        if (!$this->tipo_documento_id) {
            Notification::make()->title('Atención')->body('Debe seleccionar un tipo de documento')->warning()->send();
            return;
        }
        if (!$this->tipo_pago) {
            Notification::make()->title('Atención')->body('Debe seleccionar un tipo de pago')->warning()->send();
            return;
        }

        // Validación estricta con margen pequeño para decimales flotantes
        if (abs($this->pendiente) > 0.01) {
            Notification::make()->title('Atención')->body('El valor aplicado no coincide con el total a pagar')->warning()->send();
            return;
        }

        // Validar fecha
        if (!$this->fecha_proceso) {
             Notification::make()->title('Atención')->body('Debe seleccionar la fecha de proceso')->warning()->send();
             return;
        }

        try {
            // AJUSTE: Se añadió el parámetro de FECHA al final (signo de interrogación extra)
            // Asegúrate de actualizar tu función SQL: generar_comprobante_v2(..., p_fecha DATE)
            DB::select(
                'SELECT * FROM generar_comprobante_v2(?, ?, ?, ?, ?, ?, ?, ?)',
                [
                    (int)$this->cliente->tercero_id,
                    (int)$this->tipo_documento_id,
                    (string)$this->tipo_pago,
                    (float)$this->efectivo,
                    (float)$this->cheque,
                    (float)$this->valor_abonar,
                    (string)auth()->user()->name,
                    (string)$this->fecha_proceso // NUEVO PARÁMETRO
                ]
            );

            Notification::make()->title('Éxito')->body('Comprobante generado correctamente')->success()->send();

            // Limpieza opcional de datos si lo requieres antes de redirigir
            // $this->limpiarDatos();

            redirect()->route('filament.admin.tesoreria.resources.pago-individuals.index');

        } catch (\Exception $e) {
            Notification::make()->title('Error')->body('Error al procesar: ' . $e->getMessage())->danger()->send();
        }
    }

    protected function getFormActions(): array
    {
        return [];
    }

}
