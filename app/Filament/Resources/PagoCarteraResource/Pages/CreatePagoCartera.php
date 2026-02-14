<?php

namespace App\Filament\Resources\PagoCarteraResource\Pages;

use App\Filament\Resources\PagoCarteraResource;
use App\Models\Tercero;
use App\Models\TipoDocumentoContable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;

class CreatePagoCartera extends CreateRecord
{
    protected static string $resource = PagoCarteraResource::class;
    protected static string $view = 'custom.tesoreria.partials.create-pagos-cartera';
    protected static ?string $pollingInterval = null;

    public bool $show = false;
    public $nro_docto_actual;

    public $cliente;
    public $tipo_documento_id = null;
    public $sigla_documento;
    public $numerador;
    public $fecha_proceso;
    public float $total_a_pagar = 0;
    public float $efectivo = 0;
    public float $cheque = 0;
    public float $valor_abonar = 0;
    public float $valor_total_recibido = 0;
    public float $aplica_valor_a_total = 0;
    public float $pendiente = 0;

    public array $pagos_creditos = [];
    public array $pagos_obligaciones = [];
    public array $pagos_voluntarios = [];


    public function form(Form $form): Form
    {
        return $form
            ->columns(9)
            ->schema([
                Select::make('tipo_documento')
                    ->label('Tipo de documento')
                    ->options(TipoDocumentoContable::query()
                        ->where('uso_pago', true)
                        ->select(DB::raw("id, CONCAT(sigla, ' - ', tipo_documento) AS nombre_completo"))
                        ->pluck('nombre_completo', 'id'))
                    ->live()
                    ->columnSpan(3)
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state, Set $set) {
                        $this->tipo_documento_id = $state;
                        if ($state && $doc = TipoDocumentoContable::find($state)) {
                            $this->sigla_documento = $doc->sigla;
                            $this->numerador = $doc->numerador;
                            $set('nro_documento', $doc->numerador);
                        }
                    }),

                TextInput::make('nro_documento')
                    ->label('Nro de documento')
                    ->columnSpan(2)
                    ->disabled(),

                DatePicker::make('fecha_proceso')
                    ->label('Fecha de Proceso')
                    ->default(now())
                    ->native(false)
                    ->required()
                    ->columnSpan(2)
                    ->afterStateUpdated(fn($state) => $this->fecha_proceso = $state),

                TextInput::make('cliente')
                    ->live(onBlur: true)
                    ->prefixIcon('heroicon-c-magnifying-glass-circle')
                    ->required()
                    ->columnSpan(3)
                    ->afterStateUpdated(fn($state, Set $set) => $this->cargarCliente($state, $set)),

                TextInput::make('nombre')
                    ->columnSpan(6)
                    ->disabled(),

                Section::make('Información de pago')
                    ->columns(3)
                    ->schema([
                        TextInput::make('efectivo')
                            ->numeric()->default(0)->live(onBlur: true)
                            ->afterStateUpdated(fn(Get $get, Set $set) => $this->recalcularTotales($get, $set)),

                        TextInput::make('cheque')
                            ->numeric()->default(0)->live(onBlur: true)
                            ->afterStateUpdated(fn(Get $get, Set $set) => $this->recalcularTotales($get, $set)),

                        TextInput::make('valor_abonar')
                            ->numeric()->default(0)->live(onBlur: true)
                            ->afterStateUpdated(fn(Get $get, Set $set) => $this->recalcularTotales($get, $set)),

                        TextInput::make('valor_total')->label('Total Recibido')->readOnly(),
                        TextInput::make('pendiente_por_aplicar')
                            ->label(' Valor Pendiente')
                            ->readOnly()
                            ->suffixIconColor('success-600')
                            ->suffixIcon('heroicon-m-hand-raised')
                            ->helperText('Saldo disponible para aplicar a créditos y obligaciones.')
                            ->prefix('Pendiente por Aplicar')
                            ->formatStateUsing(fn() => number_format($this->pendiente, 2))
                            ->extraInputAttributes(fn($state) => [
                                'class' => abs($this->pendiente) > 0.01
                                    ? 'text-danger-600 font-bold animate-pulse'
                                    : 'text-success-600 font-bold'
                            ]),
                    ])
            ]);
    }

    protected function cargarCliente($state, Set $set)
    {
        if (!$state) return;
        $asociado = Tercero::where('tercero_id', $state)->first();

        if ($asociado) {
            $set('nombre', "{$asociado->nombres} {$asociado->primer_apellido} {$asociado->segundo_apellido}");
            $this->cliente = $asociado;
            $this->show = true;
            $this->limpiarDatosEnMemoria();
            $this->calcularDeudaTotal($set);
        } else {
            Notification::make()->warning()->title('Cliente no encontrado')->send();
        }
    }

    public function getCreditosVigentesProperty(): Collection
    {
        if (!$this->cliente) {
            return collect();
        }

        return DB::table('cartera_encabezados')
            ->where('cliente', $this->cliente->tercero_id)
            ->where('tdocto', 'PAG')
            ->where('vlr_saldo_actual', '>', 0)
            ->where('estado', 'A')
            ->get();
    }

    protected function calcularDeudaTotal(Set $set)
    {
        $id = $this->cliente->tercero_id;

        $cartera = DB::table('cartera_encabezados')->where('cliente', $id)->where('estado', 'A')->where('tdocto', 'PAG')->sum('vlr_congelada');
        $liq = DB::table('cartera_encabezados')->where('cliente', $id)->where('estado', 'A')->where('tdocto', 'PAG')->sum('vlr_cuentas_orden');
        $obli = DB::table('detalle_vencimiento_descuento')->where('cliente', $id)->where('estado', 'A')->sum('vlr_congelada');

        $this->total_a_pagar = (float) ($cartera + $liq + $obli);
        $set('total_a_pagar', $this->total_a_pagar);

        $this->recalcularTotales(null, $set);
    }

    public function recalcularTotales($get, Set $set)
    {
        $this->efectivo = $get ? (float)str_replace(',', '', $get('efectivo') ?? 0) : $this->efectivo;
        $this->cheque = $get ? (float)str_replace(',', '', $get('cheque') ?? 0) : $this->cheque;
        $this->valor_abonar = $get ? (float)str_replace(',', '', $get('valor_abonar') ?? 0) : $this->valor_abonar;

        $this->valor_total_recibido = $this->efectivo + $this->cheque + $this->valor_abonar;
        $this->sincronizarDistribucion();

        $set('valor_total', $this->valor_total_recibido);
        $set('pendiente_por_aplicar', $this->pendiente);

    }

    public function confirmarLiquidacion($monto)
    {
        $monto = (float)$monto;
        $ya_aplicado_otros = 0;

        foreach ($this->pagos_creditos as $id => $val) {
            if ((string)$id !== (string)$this->nro_docto_actual) {
                $ya_aplicado_otros += $val;
            }
        }
        foreach ($this->pagos_obligaciones as $val) $ya_aplicado_otros += $val;
        foreach ($this->pagos_voluntarios as $pago) $ya_aplicado_otros += $pago['valor'];

        $disponible_real = $this->valor_total_recibido - $ya_aplicado_otros;


        if ($monto > $disponible_real) {
            Notification::make()
                ->title('Monto excedido')
                ->body('No puedes aplicar $' . number_format($monto) . ' porque solo dispones de $' . number_format($disponible_real))
                ->danger()
                ->send();
            return;
        }

        if ($monto <= 0) {
            unset($this->pagos_creditos[$this->nro_docto_actual]);
            $this->sincronizarDistribucion();
            Notification::make()->title('Aplicación eliminada')->info()->send();
            return;
        }

        $this->pagos_creditos[$this->nro_docto_actual] = $monto;
        $this->sincronizarDistribucion();
    }

    public function abrirModalLiquidacion($nro_docto)
    {
        // REGLA: Garantizar que el total recibido sea mayor a 0
        if ($this->valor_total_recibido <= 0) {
            Notification::make()
                ->title('Atención')
                ->body('Primero debe ingresar el valor recibido (Efectivo/Cheque/Abono).')
                ->warning()
                ->send();
            return;
        }

        // REGLA: Garantizar que la diferencia no sea negativa (ya se aplicó de más)
        if ($this->pendiente <= 0) {
            Notification::make()
                ->title('Sin saldo disponible')
                ->body('Ya ha distribuido la totalidad del dinero recibido.')
                ->danger()
                ->send();
            return;
        }

        $this->nro_docto_actual = $nro_docto;
        $this->dispatch('open-modal', id: 'modal-liquidacion');
    }

    public function sincronizarDistribucion()
    {
        $this->aplica_valor_a_total = 0;

        foreach ($this->pagos_creditos as $monto) {
            $this->aplica_valor_a_total += (float)$monto;
        }

        foreach ($this->pagos_obligaciones as $monto) {
            $this->aplica_valor_a_total += (float)$monto;
        }

        foreach ($this->pagos_voluntarios as $pago) {
            $this->aplica_valor_a_total += (float)$pago['valor'];
        }

        $this->pendiente = $this->valor_total_recibido - $this->aplica_valor_a_total;

        $this->dispatch('refresh-form');
    }

    public function limpiarDatosEnMemoria()
    {
        $this->pagos_creditos = [];
        $this->pagos_obligaciones = [];
        $this->pagos_voluntarios = [];
        $this->aplica_valor_a_total = 0;
    }

    public function generarComprobante()
    {
        // 1. Validaciones de seguridad
        if (!$this->cliente || !$this->tipo_documento_id) {
            Notification::make()->title('Error')->body('Debe seleccionar cliente y tipo de documento.')->danger()->send();
            return;
        }

        if (abs($this->pendiente) > 0.01) {
            Notification::make()
                ->title('¡Diferencia de Saldo Detectada!')
                ->body("Aún tienes un saldo de $ " . number_format($this->pendiente, 2) . " por distribuir. El saldo pendiente debe ser exactamente 0 para poder generar el comprobante.")
                ->icon('heroicon-o-exclamation-triangle') // Icono más agresivo
                ->color('danger')
                ->persistent()
                ->send();
            return;
        }

        try {
            DB::beginTransaction();

            $usuario = auth()->user()->name;
            $fecha = \Illuminate\Support\Carbon::parse($this->fecha_proceso)->format('Y-m-d');
            $clienteCedula = $this->cliente->tercero_id;

            // Obtener ID interno para Contabilidad (Tablas: comprobantes, comprobante_lineas)
            $terceroReal = DB::table('terceros')->where('tercero_id', (string)$clienteCedula)->first();
            if (!$terceroReal) throw new \Exception("Tercero con cédula {$clienteCedula} no encontrado en contabilidad.");

            $idParaFK = $terceroReal->id;

            // Numeración con bloqueo para evitar duplicados
            $docConfig = DB::table('tipo_documento_contables')->where('id', $this->tipo_documento_id)->lockForUpdate()->first();
            $v_numerador = $docConfig->numerador;
            $v_sigla = $docConfig->sigla;

            // --- A. CABECERA CONTABLE ---
            $comprobanteId = DB::table('comprobantes')->insertGetId([
                'tipo_documento_contables_id' => $this->tipo_documento_id,
                'n_documento'           => $v_numerador,
                'tercero_id'            => $idParaFK, // ID INTERNO
                'is_plantilla'          => false,
                'descripcion_comprobante' => "Pago Individual - Asociado {$clienteCedula}  - {$this->cliente->nombres} {$this->cliente->primer_apellido} {$this->cliente->segundo_apellido}",
                'fecha_comprobante'      => $fecha,
                'estado'                 => 'Activo',
                'usuario_original'       => $usuario,
                'created_at'             => now(),
                'updated_at'             => now(),
            ]);

            // --- B. ENCABEZADO CARTERA ---
            DB::table('documentos_cancelaciones')->insert([
                'tdocto'            => $v_sigla,
                'id_proveedor'      => $v_numerador,
                'fecha_docto'       => $fecha,
                'cliente'           => $clienteCedula, // CÉDULA
                'contabilizado'     => 'N',
                'con_nota_credito'  => 1,
                'moneda'            => 1,
                'observaciones'     => "Pago Individual - Comprobante {$v_numerador}",
                'vlr_pago_efectivo' => $this->efectivo,
                'vlr_pago_cheque'   => $this->cheque,
                'vlr_pago_otros'    => $this->valor_abonar,
                'usuario_crea'      => $usuario,
            ]);

            $v_consecutivo = 1;
            $lineaContable = 1;

            // --- C. PROCESAR CADA CRÉDITO ---
            foreach ($this->pagos_creditos as $nro_docto => $monto_total_aplicado) {
                if ($monto_total_aplicado <= 0) continue;

                $detalles = DB::table('cuotas_detalles')
                    ->where('nro_docto', $nro_docto)
                    ->where('estado', 'A')
                    ->where('tdocto', 'PAG')
                    ->orderBy('nro_cuota', 'asc')
                    ->get();

                $sobrante = $monto_total_aplicado;
                $campoAbono = ($v_sigla === 'NCR') ? 'vlr_abono_ncr' : 'vlr_abono_rec';

                foreach ($detalles as $detalle) {
                    if ($sobrante <= 0) break;

                    $pago_a_este_concepto = min($sobrante, $detalle->vlr_detalle);

                    // 1. Detalle Cartera
                    DB::table('documentos_cancelaciones_detalles')->insert([
                        'tipo_documento' => $v_sigla,
                        'numero_documento' => $v_numerador,
                        'consecutivo' => $v_consecutivo++,
                        'cliente_id' => $clienteCedula,
                        'tipo_pago' => 'DVT',
                        'tipo_documento_dvt' => $detalle->tdocto,
                        'numero_documento_dvt' => $detalle->nro_docto,
                        'numero_cuota_dvt' => $detalle->nro_cuota,
                        'concepto_descuento_dvt' => $detalle->con_descuento,
                        'valor_pago' => $pago_a_este_concepto,
                    ]);

                    // 2. Update Detalle Cuota
                    $nuevoEstado = ($pago_a_este_concepto >= $detalle->vlr_detalle) ? 'C' : 'A';
                    DB::table('cuotas_detalles')->where('id', $detalle->id)->update([
                        $campoAbono => DB::raw("$campoAbono + $pago_a_este_concepto"),
                        'estado' => $nuevoEstado,
                        'fecha_pago_total' => $nuevoEstado === 'C' ? $fecha : null
                    ]);

                    // 3. Update Encabezado Cuota
                    DB::table('cuotas_encabezados')
                        ->where('nro_docto', $detalle->nro_docto)
                        ->where('nro_cuota', $detalle->nro_cuota)
                        ->where('tdocto', 'PAG')
                        ->update([
                            $campoAbono => DB::raw("$campoAbono + $pago_a_este_concepto"),
                            'estado' => DB::raw("CASE WHEN (vlr_cuota - ($campoAbono + $pago_a_este_concepto)) <= 0 THEN 'C' ELSE 'A' END"),
                            'fecha_pago_total' => DB::raw("CASE WHEN (vlr_cuota - ($campoAbono + $pago_a_este_concepto)) <= 0 THEN '$fecha'::date ELSE NULL END")
                        ]);

                    // 4. Línea Contable (Crédito)
                    $concepto = DB::table('concepto_descuentos')->where('codigo_descuento', $detalle->con_descuento)->first();

                    if ($concepto && $concepto->cuenta_contable) {
                        $puc = DB::table('pucs')->where('puc', $concepto->cuenta_contable)->first();
                        if ($puc) {
                            DB::table('comprobante_lineas')->insert([
                                'comprobante_id' => $comprobanteId,
                                'pucs_id' => $puc->id,
                                'tercero_id' => $idParaFK,
                                'descripcion_linea' => "Abono {$concepto->descripcion} Cuota {$detalle->nro_cuota}",
                                'debito' => 0,
                                'credito' => $pago_a_este_concepto,
                                'linea' => $lineaContable++,
                            ]);
                        }
                    }
                    $sobrante -= $pago_a_este_concepto;
                }

                // Actualizar Saldo Maestro
                DB::table('cartera_encabezados')->where('nro_docto', $nro_docto)->where('tdocto', 'PAG')->update([
                    'vlr_saldo_actual' => DB::raw("vlr_saldo_actual - $monto_total_aplicado"),
                    'estado' => DB::raw("CASE WHEN (vlr_saldo_actual - $monto_total_aplicado) <= 0 THEN 'C' ELSE 'A' END"),
                    'fecha_pago_total' => DB::raw("CASE WHEN (vlr_saldo_actual - $monto_total_aplicado) <= 0 THEN '$fecha'::date ELSE NULL END")
                ]);
            }

            // --- D. LÍNEA CONTABLE (Débito - Caja/Bancos) ---
            $pucCaja = DB::table('pucs')->where('puc', '110505')->first();
            if ($pucCaja) {
                DB::table('comprobante_lineas')->insert([
                    'comprobante_id' => $comprobanteId,
                    'pucs_id' => $pucCaja->id,
                    'tercero_id' => $idParaFK,
                    'descripcion_linea' => "Ingreso Recaudo Cartera",
                    'debito' => $this->valor_total_recibido,
                    'credito' => 0,
                    'linea' => $lineaContable++,
                ]);
            }

            // --- E. INCREMENTAR NUMERADOR ---
            DB::table('tipo_documento_contables')->where('id', $this->tipo_documento_id)->increment('numerador');

            DB::commit();

            // 3. LIMPIEZA DE PANTALLA Y NOTIFICACIÓN
            Notification::make()->title('Éxito')->body('Proceso completo. Comprobante: ' . $v_numerador)->success()->send();

            // Resetear propiedades para que la pantalla quede limpia
            $this->reset(['pagos_creditos', 'efectivo', 'cheque', 'valor_abonar', 'cliente', 'pendiente', 'valor_total_recibido']);

            // Opcional: Redirigir si quieres salir de la pantalla
            return redirect()->to($this->getResource()::getUrl('index'));
        } catch (\Exception $e) {
            DB::rollBack();
            Notification::make()->title('Error')->body($e->getMessage())->danger()->persistent()->send();
        }
    }

    public function getOtrasObligacionesProperty()
    {
        if (!$this->cliente) return collect();

        return DB::table('detalle_vencimiento_descuento')
            ->where('cliente', $this->cliente->tercero_id)
            ->where('estado', 'A')
            ->where('vlr_cuota', '>', 0)
            ->get();
    }


    public function previsualizarLiquidacion($monto = 0)
    {
        if (!$this->nro_docto_actual) return ['filas' => [], 'vencidas' => 0];

        $monto = (float)$monto;
        $hoy = now()->format('Y-m-d');

        $detalles = DB::table('cuotas_detalles as cd')
            ->join('cuotas_encabezados as ce', function ($join) {
                $join->on('cd.tdocto', '=', 'ce.tdocto')
                    ->on('cd.nro_docto', '=', 'ce.nro_docto')
                    ->on('cd.nro_cuota', '=', 'ce.nro_cuota');
            })
            ->join('concepto_descuentos as con', 'cd.con_descuento', '=', 'con.codigo_descuento')
            ->where('cd.nro_docto', $this->nro_docto_actual)
            ->where('cd.estado', 'A')
            ->select('cd.*', 'ce.fecha_vencimiento', 'con.descripcion as nombre_concepto')
            ->orderBy('ce.fecha_vencimiento', 'asc')
            ->orderBy('cd.con_descuento', 'asc')
            ->get();

        $cuotasPermitidas = $detalles->pluck('nro_cuota')->unique()->take(10);
        $detallesFiltrados = $detalles->whereIn('nro_cuota', $cuotasPermitidas);

        $sobrante = $monto;
        $liquidacion = [];
        $cuotasAgrupadas = $detallesFiltrados->groupBy('nro_cuota');

        foreach ($cuotasAgrupadas as $nroCuota => $conceptos) {
            $fechaVenc = $conceptos->first()->fecha_vencimiento;
            $dataCuota = [
                'nro_cuota' => $nroCuota,
                'fecha' => $fechaVenc,
                'es_vencida' => $fechaVenc <= $hoy,
                'total_deuda' => 0,
                'total_abono' => 0,
                'conceptos' => []
            ];

            foreach ($conceptos as $con) {
                $saldo = (float)$con->vlr_detalle;
                $abono = 0;
                if ($sobrante > 0 && $saldo > 0) {
                    $abono = min($sobrante, $saldo);
                    $sobrante -= $abono;
                }
                $dataCuota['total_deuda'] += $saldo;
                $dataCuota['total_abono'] += $abono;
                $dataCuota['conceptos'][] = [
                    'nombre' => $con->nombre_concepto,
                    'deuda' => $saldo,
                    'abono' => $abono
                ];
            }
            $liquidacion[] = $dataCuota;
        }

        return [
            'filas' => $liquidacion,
            'vencidas' => $detallesFiltrados->where('fecha_vencimiento', '<=', $hoy)->pluck('nro_cuota')->unique()->count()
        ];
    }
}
