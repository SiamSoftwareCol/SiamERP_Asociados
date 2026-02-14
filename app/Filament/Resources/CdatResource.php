<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\InformeSaldosCdat;
use App\Filament\Resources\CdatResource\Pages;
use App\Filament\Resources\CdatResource\RelationManagers\BeneficiariosRelationManager;
use App\Models\CertificadoDeposito;
use App\Models\ConceptoDescuento;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use App\Models\TipoDocumentoContable;
use App\Models\Comprobante;
use App\Models\ComprobanteLinea;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use NumberFormatter;
use Filament\Tables\Actions\EditAction;
use Closure;

class CdatResource extends Resource
{
    protected static ?string $model = CertificadoDeposito::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $cluster = InformeSaldosCdat::class;
    protected static ?string $modelLabel = 'CDAT - Constitución';
    protected static ?string   $pluralModelLabel = 'CDAT - Constituciones';
    protected static ?string $slug = 'Par/Tab/ConstitucionCDAT';
    protected static ?int $navigationSort = 3;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Section::make('Resumen del CDAT')
                ->description('Información general del título. Estos datos son solo de referencia para la adición de beneficiarios.')
                ->icon('heroicon-o-document-text')
                ->collapsible()
                ->schema([
                    Forms\Components\TextInput::make('numero_cdat')
                        ->label('Número del CDAT')
                        ->disabled()
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('estado')
                        ->label('Estado del Título')
                        ->disabled()
                        ->formatStateUsing(fn($state) => match ($state) {
                            'P' => 'Preconstituido',
                            'A' => 'Activo',
                            'V' => 'Vencido',
                            default => $state,
                        })
                        ->columnSpan(1),
                    Forms\Components\TextInput::make('valor')
                        ->label('Capital Invertido')
                        ->prefix('$')
                        ->disabled()
                        ->formatStateUsing(fn($state) => number_format($state, 0, ',', '.'))
                        ->columnSpan(1),
                    Forms\Components\DatePicker::make('fecha_creacion')
                        ->label('Fecha de Apertura')
                        ->disabled()
                        ->columnSpan(1),
                    Forms\Components\DatePicker::make('fecha_vencimiento')
                        ->label('Fecha de Vencimiento')
                        ->disabled()
                        ->columnSpan(1),
                ])
                ->columns(3),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(CertificadoDeposito::query()->where('estado', 'P'))
            ->columns([
                Tables\Columns\TextColumn::make('numero_cdat')->label('CDAT'),
                Tables\Columns\TextColumn::make('valor')->money('COP'),
                Tables\Columns\TextColumn::make('tasa_ea'),
                Tables\Columns\TextColumn::make('fecha_vencimiento')->date(),
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'P' => 'Pendiente',
                        'A' => 'Activo',
                        'C' => 'Cerrado',
                        default => $state,
                    })
                    ->colors([
                        'warning' => 'P',
                        'success' => 'A',
                        'danger' => 'C',
                    ]),
            ])
            ->recordUrl(null)

            ->actions([
                EditAction::make()
                    ->label('Beneficiarios')
                    ->icon('heroicon-o-plus-circle')
                    ->color('secondary'),
                Action::make('Constituir')
                    ->label('Constituir Cdat')
                    ->icon('heroicon-o-chart-bar')
                    ->color('success')
                    ->visible(fn(CertificadoDeposito $record) => $record->estado === 'P')
                    ->mountUsing(function (Forms\ComponentContainer $form, CertificadoDeposito $record) {
                        $form->fill([
                            'valor_cdat_display' => '$ ' . number_format($record->valor, 0),
                        ]);
                        if ($record->beneficiarios()->count() === 0) {
                            Notification::make()
                                ->title('Faltan Beneficiarios')
                                ->warning()
                                ->send();
                            return;
                        }
                    })

                    ->form([
                        Section::make('Origen de los Recursos')
                            ->description('La suma de los orígenes debe ser exactamente igual al valor del título.')
                            ->schema([
                                Forms\Components\Grid::make(6)
                                    ->schema([
                                        TextInput::make('valor_cdat_display')
                                            ->label('Valor del Título')
                                            ->disabled()
                                            ->dehydrated(false)
                                            ->columnSpan(2),

                                        Placeholder::make('comparativo_totales')
                                            ->label('Estado de Distribución')
                                            ->columnSpan(4)
                                            ->content(function (Get $get, CertificadoDeposito $record) {
                                                $totalCdat = $record->valor;
                                                $sumaIngresada = collect($get('origenes'))->sum('valor');
                                                $faltante = $totalCdat - $sumaIngresada;

                                                if (abs($faltante) < 0.01) return "✅ ¡Monto cuadrado!";

                                                $texto = $faltante > 0 ? "Faltan: $" : "Excedido: $";
                                                return "Total: $" . number_format($totalCdat, 0, ',', '.') .
                                                    " | " . $texto . number_format(abs($faltante), 0, ',', '.') . " Pesos";
                                            })
                                            ->live(),
                                    ]),

                                Repeater::make('origenes')
                                    ->label('Distribución de Fondos')
                                    ->schema([
                                        Select::make('concepto_id')
                                            ->placeholder('Seleccione origen...')
                                            ->options(
                                                fn() => ConceptoDescuento::where('transaccional', 'S')->get()
                                                    ->mapWithKeys(fn($item) => [$item->id => "{$item->cuenta_contable} - {$item->descripcion}"])
                                            )
                                            ->required()
                                            ->searchable()
                                            ->hiddenLabel()
                                            ->columnSpan(8),

                                        TextInput::make('valor')
                                            ->placeholder('Monto')
                                            ->numeric()
                                            ->prefix('$')
                                            ->required()
                                            ->hiddenLabel()
                                            ->columnSpan(4),
                                    ])
                                    ->columns(12)
                                    ->grid(1)
                                    ->reorderable(false)
                                    ->addActionLabel('Añadir Origen')
                                    ->columnSpanFull()
                                    ->extraAttributes(['class' => 'gap-y-0'])
                                    ->itemLabel(fn(array $state): ?string => null)
                                    ->rules([
                                        fn(Get $get, CertificadoDeposito $record): Closure => function (string $attribute, $value, Closure $fail) use ($record) {
                                            $suma = collect($value)->sum('valor');
                                            if (abs($suma - $record->valor) > 0.01) {
                                                $fail("La suma ($" . number_format($suma, 0) . ") no coincide con el título.");
                                            }
                                        },
                                    ]),
                            ])
                    ])

                    ->action(
                        function (CertificadoDeposito $record, array $data) {
                            try {

                                DB::transaction(function () use ($record, $data) {

                                    $asociado = $record->asociado;

                                    if (!$asociado || !$asociado->tercero_id) {
                                        throw new \Exception("No se encontró el tercero_id vinculado al asociado con código: {$record->titular_id}");
                                    }
                                    $terceroIdReal = $asociado->tercero_id;
                                    $tipoDoc = DB::table('tipo_documento_contables')->where('id', 22)->lockForUpdate()->first();
                                    if (!$tipoDoc) throw new \Exception("Tipo de documento ID 22 no encontrado.");
                                    $nuevoNumero = $tipoDoc->numerador;

                                    // 2. Crear Encabezado
                                    $comprobanteId = DB::table('comprobantes')->insertGetId([
                                        'tipo_documento_contables_id' => 22,
                                        'n_documento'                => $nuevoNumero,
                                        'tercero_id'                 => $terceroIdReal,
                                        'is_plantilla'               => false,
                                        'descripcion_comprobante'    => "Constitucion de CDAT No. {$record->numero_cdat}",
                                        'fecha_comprobante'          => $record->fecha_creacion,
                                        'created_at'                 => now(),
                                        'updated_at'                 => now(),
                                        'estado'                     => 'Activo',
                                        'usuario_original'           => auth()->user()->name,
                                    ]);

                                    $numeroLinea = 1;

                                    $codigoPucPasivo = $record->cdatTipo->puc_contable;
                                    $pucPasivo = DB::table('pucs')->where('puc', $codigoPucPasivo)->first();

                                    if (!$pucPasivo) throw new \Exception("La cuenta de pasivo {$codigoPucPasivo} no existe en la tabla PUCs.");

                                    DB::table('comprobante_lineas')->insert([
                                        'comprobante_id'    => $comprobanteId,
                                        'pucs_id'           => $pucPasivo->id,
                                        'tercero_id'        => $terceroIdReal,
                                        'descripcion_linea' => "Constitucion de CDAT No {$record->numero_cdat}",
                                        'debito'            => 0,
                                        'credito'           => $record->valor,
                                        'linea'             => $numeroLinea++,
                                        'created_at'        => now(),
                                        'updated_at'        => now(),
                                    ]);


                                    foreach ($data['origenes'] as $item) {
                                        $concepto = ConceptoDescuento::find($item['concepto_id']);


                                        $pucOrigen = DB::table('pucs')->where('puc', $concepto->cuenta_contable)->first();

                                        if (!$pucOrigen) {
                                            throw new \Exception("La cuenta de origen {$concepto->cuenta_contable} no existe en la tabla PUCs.");
                                        }

                                        DB::table('comprobante_lineas')->insert([
                                            'comprobante_id'    => $comprobanteId,
                                            'pucs_id'           => $pucOrigen->id,
                                            'tercero_id'        => $terceroIdReal,
                                            'descripcion_linea' => "Constitucion de CDAT No {$record->numero_cdat}",
                                            'debito'            => $item['valor'],
                                            'credito'           => 0,
                                            'linea'             => $numeroLinea++,
                                            'created_at'        => now(),
                                            'updated_at'        => now(),
                                        ]);
                                    }

                                    DB::table('tipo_documento_contables')->where('id', 22)->increment('numerador');
                                    $record->update(['estado' => 'A']);
                                });

                                Notification::make()->title('Contabilizado exitosamente')->success()->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Error de Contabilización')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->persistent()
                                    ->send();
                            }
                        }
                    )
                    ->modalHeading('Constitución de CDAT')
                    ->modalSubmitActionLabel('Contabilizar y Finalizar'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            BeneficiariosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCdats::route('/'),
            'create' => Pages\CreateCdat::route('/create'),
            'edit' => Pages\EditCdat::route('/{record}/edit'),
        ];
    }
}
