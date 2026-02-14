<?php

namespace App\Filament\Resources\PagoColectivoResource\Pages;

use App\Filament\Resources\PagoColectivoResource;
use App\Models\CarteraEncabezado;
use App\Models\Comprobante;
use App\Models\ComprobanteLinea;
use App\Models\CreditoLinea;
use App\Models\Pagaduria;
use App\Models\Puc;
use App\Models\Tercero;
use App\Models\TipoDocumentoContable;
use Carbon\Carbon;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;

class CreatePagoColectivo extends CreateRecord
{
    protected static string $resource = PagoColectivoResource::class;
    protected static string $view = 'custom.tesoreria.create-pagos-colectivo';
    protected static ?string $pollingInterval = null;

    public bool $show = false;
    public bool $showpag = false;
    public $cliente;
    public $pagaduria;
    public $concepto_descuento;
    public $efectivo = 0;
    public $cheque = 0;
    public $valor_abonar = 0;
    public $aplica_valor_a_total = 0;
    public $tipo_documento_id = null;
    public $cuentaCapital;
    public $tipo_pago = null;
    public $pendiente = 0;
    public $nro_docto_actual;
    public $total_a_pagar;
    public $credito_aplicados;
    public $sigla_documento;
    public $numerador;


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
                    ->required(function (Get $get, Set $set) {
                        $this->tipo_documento_id = $get('tipo_documento');
                        if ($this->tipo_documento_id) {
                            $numerador = TipoDocumentoContable::where('id', $this->tipo_documento_id)->first();
                            $this->sigla_documento = $numerador->sigla;
                            $this->numerador = $numerador->numerador;
                            $set('nro_documento', $numerador->numerador);
                        }
                        return false;
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
                    ->required(function (Get $get) {
                        if ($get('tipo_pago')) {
                            $concepto = DB::table('concepto_descuentos')
                                ->where('id', $get('tipo_pago'))
                                ->first();
                            $this->tipo_pago = $concepto->cuenta_contable;
                        }
                        return true;
                    }),
                TextInput::make('fecha')
                    ->prefixIcon('heroicon-c-calendar-days')
                    ->disabled()->default(now()->format('Y-m-d')),

                Select::make('pagaduria')
                    ->label('Pagaduria')
                    ->options(Pagaduria::query()
                        ->select(DB::raw("id, CONCAT(codigo, ' - ', nombre) AS nombre_pagaduria"))
                        ->pluck('nombre_pagaduria', 'id'))
                    ->live()
                    ->columnSpan(2)
                    ->searchable()
                    ->required(function (Get $get, Set $set) {
                        $this->pagaduria = $get('pagaduria');
                        $this->showpag = true;
                        return false;
                    }),
                TextInput::make('cliente')
                    ->live(onBlur: true)
                    ->placeholder('Nro identificación cliente')
                    ->prefixIcon('heroicon-c-magnifying-glass-circle')
                    ->required(function (Get $get, Set $set) {
                        $valor = $get('cliente');
                        if ($valor) {
                            $asociado = Tercero::where('tercero_id', $valor)->first();

                            if ($asociado) {

                                $set('nombre', $asociado->nombres . ' ' . $asociado->primer_apellido . ' ' . $asociado->segundo_apellido);
                                $set('direccion', $asociado->direccion);
                                $set('telefono', $asociado->celular);

                                $this->cliente = $asociado;
                                //$this->limpiarDatos();
                                $this->show = true;
                            }
                        }
                        return false;
                    }),

                Section::make('Información de pago')
                    ->schema([
                        TextInput::make('efectivo')
                            ->placeholder('Monto efectivo')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->disabled(function (Get $get) {
                                $this->efectivo = (float)$get('efectivo');
                                return false;
                            }),
                        TextInput::make('cheque')
                            ->placeholder('Nro cheque')
                            ->prefixIcon('heroicon-c-credit-card')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->disabled(function (Get $get) {
                                $this->cheque = (float)$get('cheque');
                                return false;
                            }),
                        TextInput::make('valor_abonar')
                            ->placeholder('Valor a abonar')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->disabled(function (Get $get) {
                                $this->valor_abonar = (float)$get('valor_abonar');
                                return false;
                            }),
                        TextInput::make('valor_total')
                            ->placeholder('Valor total')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->readOnly()
                            ->disabled(function (Get $get, Set $set) {
                                // Obtener los valores y convertirlos a números
                                $e = (float)$get('efectivo'); // Convertir a float
                                $c = (float)$get('cheque');   // Convertir a float
                                $va = (float)$get('valor_abonar'); // Convertir a float

                                // Realizar la suma
                                $vt = $e + $c + $va;

                                // Establecer el valor total
                                $set('valor_total', $vt);

                                return false;
                            }),
                        TextInput::make('pendiente_por_aplicar')
                            ->placeholder('Pendiente por aplicar')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->readOnly()
                            ->disabled(function (Get $get, Set $set): bool {
                                // Convertir los valores a float antes de restar
                                $valorTotal = (float)$get('valor_total');
                                $totalAPagar = (float)$get('total_a_pagar');

                                // Realizar la resta
                                $pendientePorAplicar = $valorTotal - $totalAPagar;

                                $this->pendiente = (float)$pendientePorAplicar;

                                // Establecer el valor de "pendiente_por_aplicar"
                                $set('pendiente_por_aplicar', $pendientePorAplicar);

                                return false;
                            }),
                        TextInput::make('total_a_pagar')
                            ->placeholder('Total a pagar')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->inputMode('decimal')
                            ->prefix('$')
                            ->live(onBlur: true)
                            ->disabled(function (Set $set): bool {

                                if ($this->cliente) {
                                    $sumatoria = DB::table('cartera_encabezados')
                                        ->where('cliente', $this->cliente->tercero_id)
                                        ->where('estado', 'A')
                                        ->where('tdocto', 'PAG')
                                        ->sum('vlr_congelada');


                                    $sumatoriaLiquidacion = DB::table('cartera_encabezados')
                                        ->where('cliente', $this->cliente->tercero_id)
                                        ->where('estado', 'A')
                                        ->where('tdocto', 'PAG')
                                        ->sum('vlr_cuentas_orden');

                                    $sumatoriaObligaciones = DB::table('detalle_vencimiento_descuento')
                                        ->where('cliente', $this->cliente->tercero_id)
                                        ->where('estado', 'A')
                                        ->sum('vlr_congelada');

                                    $otros_conceptos = DB::table('tmp_vencimiento_descuento')
                                        ->where('cliente', $this->cliente->tercero_id)
                                        ->sum('valor');

                                    $total_a_pagar = $sumatoria + $otros_conceptos + $sumatoriaLiquidacion + $sumatoriaObligaciones + $this->aplica_valor_a_total;
                                    $this->total_a_pagar = (float)$total_a_pagar;
                                    //dd($sumatoria, $sumatoriaLiquidacion, $sumatoriaObligaciones, $otros_conceptos, $this->aplica_valor_a_total);
                                    $set('total_a_pagar', $total_a_pagar);
                                }

                                return false;
                            }),
                    ])->columns(3)
            ])
            ->columns(4);
    }

    public function nroDoctoActual($nro_docto)
    {
        $this->nro_docto_actual = $nro_docto;
    }

    public function updateValorAplicado($nuevo_valor, $id)
    {
        DB::table('detalle_vencimiento_descuento')->where('id', $id)->update([
            'vlr_congelada' => $nuevo_valor
        ]);
    }

    public function calcularIntereses(int $nro_docto)
    {
        //dd($nro_docto);
        if (!$nro_docto) {
            Notification::make()
                ->title('Atención')
                ->icon('heroicon-m-exclamation-circle')
                ->body('No se puede calcular interés del crédito seleccionado')
                ->warning()
                ->duration(5000)
                ->send();
        }

        // Obtenemos las cuotas
        $cuotasEncabezado = DB::table('cuotas_encabezados')
            ->where('nro_docto', $nro_docto)
            ->where('tdocto', 'PAG')
            ->where('estado', 'C')
            ->orderBy('fecha_vencimiento', 'desc')
            ->get();

        $interes = 0;

        // Validamos que haya al menos una cuota
        if ($cuotasEncabezado->isEmpty()) {
            $carteraEncabezado = DB::table('cartera_encabezados')
                ->where('nro_docto', $nro_docto)
                ->first();

            // Tomamos la fecha de vencimiento de la primera cuota
            $primeraFechaVencimiento = Carbon::parse($carteraEncabezado->fecha_desembolso);


            // Calculamos los días de mora con base en la primera fecha
            $diasMora = $primeraFechaVencimiento->diffInDays(Carbon::parse(now()));

            // Calculamos el interés para esta cuota
            $interes += $carteraEncabezado->vlr_saldo_actual * ($diasMora * $carteraEncabezado->interes_mora);
        } else {
            // Tomamos la fecha de vencimiento de la primera cuota
            $primeraFechaVencimiento = Carbon::parse($cuotasEncabezado[0]->fecha_vencimiento);

            foreach ($cuotasEncabezado as $cuota) {
                // Calculamos los días de mora con base en la primera fecha
                $diasMora = $primeraFechaVencimiento->diffInDays(Carbon::parse(now()));

                // Calculamos el interés para esta cuota
                $interes += $cuota->vlr_cuota * ($diasMora * $cuota->interes_mora);
            }
        }



        // Retornamos los datos de interés calculado
        return [
            'primera_fecha' => $primeraFechaVencimiento,
            'segunda_fecha' => now(),
            'interes_mora' => number_format($interes, 2)
        ];
    }

    public function aplicarValor(int $nro_docto, float $valorAplicado)
    {
        DB::table('cartera_encabezados')->where('nro_docto', $nro_docto)->update([
            'vlr_congelada' => floatval($valorAplicado)
        ]);
    }

    public function aplicarValorLiquidacion(int $nro_docto, float $valorAplicado)
    {
        //dd($nro_docto, $valorAplicado);
        DB::table('cartera_encabezados')->where('nro_docto', $nro_docto)->update([
            'vlr_cuentas_orden' => floatval($valorAplicado)
        ]);
    }

    public function generarLiquidacion(int $nro_docto)
    {
        // Paso 1: Obtener el valor de "limite_cuotas_pagar" desde la tabla general_settings
        $limiteConfig = DB::table('general_settings')
            ->orderBy('id', 'asc')
            ->value('more_configs->limite_cuotas_pagar'); // Accede a la propiedad JSON

        // Paso 2: Calcular el límite dinámico (multiplicar por 4)
        $limiteDinamico = $limiteConfig ? $limiteConfig * 4 : 20; // Si no hay valor, usa 20 como valor por defecto

        // Paso 3: Aplicar el límite dinámico en la consulta principal
        return DB::table('cuotas_detalles as cds')
            ->join('concepto_descuentos as cd', 'cds.con_descuento', '=', 'cd.codigo_descuento')
            ->join('cartera_composicion_conceptos as ccc', function ($join) {
                $join->on('ccc.tipo_documento', '=', 'cds.tdocto')
                    ->on('ccc.numero_documento', '=', 'cds.nro_docto')
                    ->on('cds.con_descuento', '=', 'ccc.concepto_descuento');
            })
            ->where('cds.tdocto', 'PAG')
            ->where('cds.nro_docto', $nro_docto)
            ->where('cds.estado', 'A')
            ->orderBy('cds.nro_cuota')
            ->orderBy('ccc.prioridad')
            ->select(
                'cds.id',
                'cds.nro_docto',
                'cds.nro_cuota',
                'cd.descripcion',
                'ccc.prioridad',
                DB::raw('cds.vlr_detalle - (cds.vlr_abono_ncr + cds.vlr_abono_rec + cds.vlr_abono_dpa + cds.vlr_descuento) as vlr_detalle'),
                'cds.vlr_cuentas_orden',
                'cds.con_descuento'
            )
            ->limit($limiteDinamico) // Aplicar el límite dinámico
            ->get();
    }

    public function aplicaValorLiquidacion(int $nro_docto, array $cuotas)
    {
        //dd($nro_docto, $cuotas);
        foreach ($cuotas as $cuota) {
            DB::table('cuotas_detalles')
                ->where('nro_docto', $nro_docto)
                ->where('id', $cuota['id'])
                ->update([
                    'vlr_cuentas_orden' => isset($cuota['vlr_aplicar']) ? $cuota['vlr_aplicar'] : 0.00
                ]);
        }
    }

    public function vencimientoDescuento($vencimiento)
    {
        //dd($vencimiento);
        // Validamos que ya exista un registro en la base de datos
        $existe = DB::table('tmp_vencimiento_descuento')
            ->where('cliente', $vencimiento['cliente'])
            ->where('puc', $vencimiento['cuenta_contable'])
            ->where('codigo_concepto', $vencimiento['codigo_concepto'])
            ->first();

        if ($existe) {
            // Si existe, actualizamos el registro
            DB::table('tmp_vencimiento_descuento')
                ->where('id', $existe->id) // Usamos el ID del registro encontrado
                ->update([
                    'valor' => $vencimiento['valor']
                ]);
            return;
        }

        // Si no existe, insertamos un nuevo registro
        DB::table('tmp_vencimiento_descuento')->insert([
            'cliente' => $vencimiento['cliente'],
            'puc' => $vencimiento['cuenta_contable'],
            'valor' => $vencimiento['valor'],
            'codigo_concepto' => $vencimiento['codigo_concepto'],
            'descripcion' => $vencimiento['descripcion'],
        ]);

        return;
    }

    public function eliminaVencimiento($descuento)
    {
        DB::table('tmp_vencimiento_descuento')
            ->where('id', $descuento)
            ->delete();

        Notification::make()
            ->title('Atención')
            ->icon('heroicon-m-trash')
            ->body('Descuento eliminado correctamente')
            ->success()
            ->duration(5000)
            ->send();
    }

    public function limpiarDatos()
    {
        if ($this->cliente) {
            DB::table('cartera_encabezados')
                ->where('cliente', $this->cliente->tercero_id)
                ->where('estado', 'A')
                ->where('tdocto', 'PAG')
                ->update([
                    'vlr_congelada' => 0.00
                ]);


            DB::table('cartera_encabezados')
                ->where('cliente', $this->cliente->tercero_id)
                ->where('estado', 'A')
                ->where('tdocto', 'PAG')
                ->update([
                    'vlr_cuentas_orden' => 0.00
                ]);

            DB::table('detalle_vencimiento_descuento')
                ->where('cliente', $this->cliente->tercero_id)
                ->where('estado', 'A')
                ->update([
                    'vlr_congelada' => 0.00
                ]);

            DB::table('tmp_vencimiento_descuento')
                ->where('cliente', $this->cliente->tercero_id)
                ->update([
                    'valor' => 0.00
                ]);


            DB::table('cuotas_detalles')
                ->join('cartera_encabezados as ce', 'ce.nro_docto', '=', 'cuotas_detalles.nro_docto')
                ->where('ce.cliente', $this->cliente->tercero_id)
                ->where('ce.estado', 'A')
                ->where('cuotas_detalles.tdocto', 'PAG')
                ->update([
                    'vlr_cuentas_orden' => 0.00
                ]);
        }
    }

    public function generarComprobante()
    {

        // Validar campos
        if (!$this->cliente) {
            Notification::make()
                ->title('Atención')
                ->icon('heroicon-m-exclamation-circle')
                ->body('Debe seleccionar un cliente')
                ->warning()
                ->duration(5000)
                ->send();
            return;
        }

        if (!$this->tipo_documento_id) {
            Notification::make()
                ->title('Atención')
                ->icon('heroicon-m-exclamation-circle')
                ->body('Debe seleccionar un tipo de documento')
                ->warning()
                ->duration(5000)
                ->send();
            return;
        }

        if (!$this->tipo_pago) {
            Notification::make()
                ->title('Atención')
                ->icon('heroicon-m-exclamation-circle')
                ->body('Debe seleccionar un tipo de pago')
                ->warning()
                ->duration(5000)
                ->send();
            return;
        }

        if ($this->pendiente > 0) {
            Notification::make()
                ->title('Atención')
                ->icon('heroicon-m-exclamation-circle')
                ->body('El valor pendiente por aplicar no es igual al total a pagar')
                ->warning()
                ->duration(5000)
                ->send();
            return;
        }

        try {

            DB::select(
                'SELECT * FROM generar_comprobante_v2(?, ?, ?, ?, ?, ?, ?)',
                [(int)$this->cliente->tercero_id, (int)$this->tipo_documento_id,  (string)$this->tipo_pago, (float)$this->efectivo ?? 0.00, (float)$this->cheque ?? 0.00, (float)$this->valor_abonar ?? 0.00, (string)auth()->user()->name]
            );

            redirect()->route('filament.admin.tesoreria.resources.pago-individuals.index');

            Notification::make()
                ->title('Atención')
                ->icon('heroicon-m-check-circle')
                ->body('Comprobante generado correctamente')
                ->success()
                ->duration(5000)
                ->send();
        } catch (\Exception $e) {
            //dd($e);
            $this->dispatch('close-modal', id: 'modal-loading');
            sleep(2);
            $this->dispatch('open-modal', id: 'modal-error');
            return;
        } finally {
            $this->dispatch('close-modal', id: 'modal-loading');
            sleep(2);
            $this->dispatch('open-modal', id: 'modal-success');
            return;
        }
    }

    protected function getFormActions(): array
    {
        return [];
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }


}
