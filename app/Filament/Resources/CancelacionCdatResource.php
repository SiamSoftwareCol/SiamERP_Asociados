<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\InformeSaldosCdat;
use App\Models\CertificadoDeposito;
use App\Models\ConceptoDescuento;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use App\Filament\Resources\CancelacionCdatResource\Pages;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Get;
use Filament\Forms\Components\Select;
use Illuminate\Support\HtmlString;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\DB;

class CancelacionCdatResource extends Resource
{
    protected static ?string $model = CertificadoDeposito::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $cluster = InformeSaldosCdat::class;
    protected static ?string $modelLabel = 'Redimir CDATs';
    protected static ?int $navigationSort = 4;

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(CertificadoDeposito::query()->where('estado', 'A')) // Simplificado para pruebas
            ->defaultSort('fecha_vencimiento', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('numero_cdat')
                    ->label('CDAT')
                    ->searchable()
                    ->copyable()
                    ->weight('bold')
                    ->description(fn($record) => $record->asociado->tercero?->nombre_completo ?? 'Sin asociado'),
                Tables\Columns\TextColumn::make('valor')
                    ->money('COP')
                    ->label('Capital')
                    ->alignment('center')
                    ->weight('bold'),
                Tables\Columns\TextColumn::make('fecha_vencimiento')
                    ->date('d/m/Y')
                    ->label('Vencimiento')
                    ->sortable()
                    ->alignment('center')
                    ->icon('heroicon-m-calendar'),
                Tables\Columns\TextColumn::make('dias_vencimiento')
                    ->label('Días para Vencer')
                    ->getStateUsing(fn($record) => now()->startOfDay()->diffInDays($record->fecha_vencimiento, false))
                    ->badge()
                    ->color(fn($state): string => match (true) {
                        $state <= 0 => 'danger',
                        $state <= 2 => 'warning',
                        default => 'success',
                    })
                    ->formatStateUsing(fn($state) => $state <= 0 ? "Vencido " . abs($state) . " días" : "Faltan $state días")
                    ->alignment('center'),
            ])
            ->actions([
                Action::make('cancelar')
                    ->label('Pagar CDAT')
                    ->icon('heroicon-o-check-badge')
                    ->color('Slate')
                    ->modalHeading('Liquidación de Cancelación')
                    ->mountUsing(fn(Forms\ComponentContainer $form, CertificadoDeposito $record) => $form->fill([
                        'interes_generado' => $record->intereses_generados ?? 0,
                        'retencion_fuente' => $record->valor_retencion ?? 0,
                    ]))
                    ->form([
                        Section::make('Resumen de Liquidación')

                            ->schema([
                                Placeholder::make('resumen')
                                    ->label('')
                                    ->content(function (Get $get, CertificadoDeposito $record) {
                                        $cap = (float) $record->valor;
                                        $int = (float) $get('interes_generado');
                                        $ret = (float) $get('retencion_fuente');
                                        $neto = ($cap + $int) - $ret;

                                        return new HtmlString("
                <div class='flex flex-wrap items-center gap-6 text-sm border-t border-b py-3 dark:border-gray-700'>
                    <div class='flex gap-2'>
                        <span class='text-gray-500'>Capital:</span>
                        <span class='font-bold'>$ " . number_format($cap, 2) . "</span>
                    </div>
                    <div class='flex gap-2'>
                        <span class='text-gray-500'>Intereses:</span>
                        <span class='font-bold text-success-600'>+ $ " . number_format($int, 2) . "</span>
                    </div>
                    <div class='flex gap-2'>
                        <span class='text-gray-500'>Retención:</span>
                        <span class='font-bold text-danger-600'>- $ " . number_format($ret, 2) . "</span>
                    </div>
                    <div class='flex gap-2 ml-auto border-l pl-6 dark:border-gray-700'>
                        <span class='text-gray-500 font-medium'>Total a pagar:</span>
                        <span class='text-xl font-black text-primary-600'>$ " . number_format($neto, 2) . "</span>
                    </div>
                </div>
            ");
                                    }),
                            ]),

                        Section::make('Distribución del Pago')
                            ->schema([
                                Repeater::make('destinos')
                                    ->label('Cuentas de Destino')
                                    ->schema([
                                        Select::make('concepto_id')
                                            ->label('Concepto')
                                            ->options(ConceptoDescuento::where('transaccional', 'S') ->pluck('descripcion', 'id'))

                                            ->required()
                                            ->columnSpan(8),
                                        TextInput::make('valor')
                                            ->numeric()
                                            ->required()
                                            ->prefix('$')
                                            ->columnSpan(4),
                                    ])
                                    ->defaultItems(1)
                                    ->columns(12)
                                    ->addActionLabel('Agregar otro destino')
                                    // Validación simplificada de suma
                                    ->rules([
                                        fn(Get $get, CertificadoDeposito $record): \Closure => function (string $attribute, $value, \Closure $fail) use ($get, $record) {
                                            $neto = ($record->valor + $get('interes_generado')) - $get('retencion_fuente');
                                            $suma = collect($value)->sum(fn($i) => (float)($i['valor'] ?? 0));
                                            if (abs($suma - $neto) > 0.01) {
                                                $fail("La suma ($" . number_format($suma, 2) . ") no coincide con el neto ($" . number_format($neto, 2) . ")");
                                            }
                                        },
                                    ]),
                            ])
                    ])

                    ->action(function (CertificadoDeposito $record, array $data) {
                        try {
                            // 1. Capturamos el ID que retorna la transacción
                            $comprobanteId = DB::transaction(function () use ($record, $data) {
                                $asociado = $record->asociado;
                                $terceroId = $asociado->tercero_id;
                                $valorCapital = (float) $record->valor;
                                $interesGenerado = (float) ($record->intereses_generados ?? 0);
                                $retencionFuente = (float) ($record->valor_retencion ?? 0);

                                $tipoDoc = DB::table('tipo_documento_contables')->where('id', 17)->lockForUpdate()->first();

                                $id = DB::table('comprobantes')->insertGetId([
                                    'tipo_documento_contables_id' => 17,
                                    'n_documento'                => $tipoDoc->numerador,
                                    'tercero_id'                 => $terceroId,
                                    'descripcion_comprobante'    => "Cancelación CDAT No. {$record->numero_cdat}",
                                    'fecha_comprobante'          => now(),
                                    'estado'                     => 'Activo',
                                    'usuario_original'           => auth()->user()->name,
                                    'created_at'                 => now(),
                                    'updated_at'                 => now(),
                                ]);

                                $linea = 1;

                                // Débito Capital
                                DB::table('comprobante_lineas')->insert([
                                    'comprobante_id'    => $id,
                                    'pucs_id'           => DB::table('pucs')->where('puc', $record->cdatTipo->puc_contable)->value('id'),
                                    'tercero_id'        => $terceroId,
                                    'descripcion_linea' => "Cancelación Capital CDAT",
                                    'debito'            => $valorCapital,
                                    'credito' => 0,
                                    'linea' => $linea++,
                                    'created_at' => now(),
                                ]);

                                // Débito Intereses
                                if ($interesGenerado > 0) {
                                    DB::table('comprobante_lineas')->insert([
                                        'comprobante_id'    => $id,
                                        'pucs_id'           => DB::table('pucs')->where('puc', $record->cdatTipo->puc_contable_interes)->value('id'),
                                        'tercero_id'        => $terceroId,
                                        'descripcion_linea' => "Gasto Intereses",
                                        'debito'            => $interesGenerado,
                                        'credito' => 0,
                                        'linea' => $linea++,
                                        'created_at' => now(),
                                    ]);
                                }

                                // Crédito Retención
                                if ($retencionFuente > 0) {
                                    DB::table('comprobante_lineas')->insert([
                                        'comprobante_id'    => $id,
                                        'pucs_id'           => DB::table('pucs')->where('puc', $record->cdatTipo->puc_contable_retencion)->value('id'),
                                        'tercero_id'        => $terceroId,
                                        'descripcion_linea' => "Retención en la Fuente",
                                        'debito'            => 0,
                                        'credito' => $retencionFuente,
                                        'linea' => $linea++,
                                        'created_at' => now(),
                                    ]);
                                }

                                // Créditos Destinos
                                foreach ($data['destinos'] as $dest) {
                                    $concepto = ConceptoDescuento::find($dest['concepto_id']);
                                    DB::table('comprobante_lineas')->insert([
                                        'comprobante_id'    => $id,
                                        'pucs_id'           => DB::table('pucs')->where('puc', $concepto->cuenta_contable)->value('id'),
                                        'tercero_id'        => $terceroId,
                                        'descripcion_linea' => "Pago: {$concepto->descripcion}",
                                        'debito'            => 0,
                                        'credito' => (float) $dest['valor'],
                                        'linea' => $linea++,
                                        'created_at' => now(),
                                    ]);
                                }

                                DB::table('tipo_documento_contables')->where('id', 17)->increment('numerador');
                                $record->update(['estado' => 'C', 'fecha_cancelacion' => now()]);

                                return $id; // <--- Retornamos el ID al finalizar la transacción
                            });

                            // 2. Ahora que ya tenemos el ID, notificamos e imprimimos
                            Notification::make()->title('CDAT Cancelado y Contabilizado')->success()->send();

                            return static::imprimirComprobante($comprobanteId);
                        } catch (\Exception $e) {
                            Notification::make()->title('Error')->body($e->getMessage())->danger()->persistent()->send();
                        }
                    })
            ]);
    }

    public static function imprimirComprobante($comprobanteId)
    {
        $comprobanteModel = \App\Models\Comprobante::with(['tipoDocumentoContable', 'tercero'])
            ->find($comprobanteId);

        if (!$comprobanteModel) {
            throw new \Exception("Comprobante no encontrado.");
        }

        $data = [
            'n_documento'                => $comprobanteModel->n_documento,
            'fecha_comprobante'          => $comprobanteModel->fecha_comprobante,
            'descripcion_comprobante'    => $comprobanteModel->descripcion_comprobante,
            'tipo_documento_contables_id' => $comprobanteModel->tipo_documento_contables_id,
            'tipoDocumentoContable'      => $comprobanteModel->tipoDocumentoContable?->tipo_documento ?? 'N/A',
            'tercero'                    => $comprobanteModel->tercero?->nombre_completo ?? $comprobanteModel->tercero?->nombres,
            'comprobanteLinea'           => \App\Models\ComprobanteLinea::with(['puc', 'tercero'])
                ->where('comprobante_id', $comprobanteId)
                ->get(),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.comprobante', $data);


        return response()->stream(
            fn() => print($pdf->output()),
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="comprobante.pdf"',
            ]
        );
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCancelacionCdats::route('/'),
        ];
    }

    // Si tenías el método getCancelacionQuery, asegúrate de que esté dentro de la clase
    public static function getCancelacionQuery()
    {
        return CertificadoDeposito::query()
            ->where('estado', 'A')
            ->whereDate('fecha_vencimiento', '<=', now()->addDays(5));
    }
}
