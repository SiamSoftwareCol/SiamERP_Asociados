<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ParametrosCartera;
use App\Filament\Resources\CreditoSolicitudResource\Pages;
use App\Models\CreditoSolicitud;
use App\Models\Tercero;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class CreditoSolicitudResource extends Resource
{
    protected static ?string $model = CreditoSolicitud::class;
    protected static ?string $cluster = ParametrosCartera::class;
    protected static ?string $navigationIcon = 'heroicon-s-currency-dollar';
    protected static ?string $navigationLabel = 'Aprobaciones de Solicitudes';
    protected static ?string $modelLabel = 'Aprobaciones de Solicitudes';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Información de la Solicitud')
                ->description('Detalles del titular y condiciones financieras de la solicitud.')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Forms\Components\Grid::make(12)
                        ->schema([
                            Forms\Components\Placeholder::make('id_display')
                                ->label('No. Solicitud')
                                ->content(fn ($record) => $record->id)
                                ->extraAttributes([
                                        'class' => 'bg-gray-50 dark:bg-white/5 p-2 rounded-lg border border-gray-200 dark:border-white/10 shadow-sm'
                                    ])
                                ->columnSpan(2),
                            Forms\Components\Placeholder::make('titular_nombre')
                                ->label(' Titular Credito')
                                ->content(function ($record) {
                                    return $record->terceroAsociado?->nombre_completo
                                        ?? $record->terceroAsociado?->nombre
                                        ?? 'Nombre no encontrado';
                                })
                                ->extraAttributes([
                                        'class' => 'bg-gray-50 dark:bg-white/5 p-2 rounded-lg border border-gray-200 dark:border-white/10 shadow-sm'
                                    ])
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('vlr_solicitud')
                                ->label('Valor Solicitado')
                                ->disabled()
                                ->prefix('$')
                                ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                ->columnSpan(2),
                            Forms\Components\DatePicker::make('fecha_solicitud')
                                ->label('Fecha Solicitud')
                                ->disabled()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('linea_id')
                                ->label('Línea de Crédito')
                                ->formatStateUsing(fn ($record) => $record->lineaCredito?->descripcion)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('pagaduria_id')
                                ->label('Pagaduría')
                                ->formatStateUsing(fn ($record) => $record->empresaCredito?->nombre)
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('tipo_desembolso')
                                ->label('Tipo Desembolso')
                                ->formatStateUsing(fn ($state) => match ($state) {
                                    'V' => 'Pago Ventanilla',
                                    'N' => 'Descuento por Nómina',
                                    default => $state,
                                })
                                ->disabled()
                                ->dehydrated(false)
                                ->columnSpan(4),
                            Forms\Components\TextInput::make('tasa_id')
                                ->label('Tasa Interes Cte')
                                ->disabled()
                                ->columnSpan(2),
                            Forms\Components\TextInput::make('nro_cuotas_max')
                                ->label('No. Cuotas')
                                ->disabled()
                                ->columnSpan(1),
                            Forms\Components\DatePicker::make('fecha_primer_vto')
                                ->label('Primer Vencimiento')
                                ->disabled()
                                ->columnSpan(3),
                        ]),
                ]),

            // SECCIÓN DE DECISIÓN (DILIGENCIABLE)
            Forms\Components\Section::make('Decisión de Aprobación')
                ->icon('heroicon-o-check-badge')
                ->schema([
                    Forms\Components\Grid::make(2)
                        ->schema([
                            Forms\Components\Select::make('ente_aprobador')
                                ->label('Ente que Aprueba')
                                ->options([
                                    'JUNTA DIRECTIVA' => 'Junta Directiva',
                                    'GERENCIA' => 'Gerencia',
                                    'COMITE DE CREDITO' => 'Comité de Crédito',
                                ])
                                ->required()
                                ->native(false),

                            Forms\Components\TextInput::make('nro_acta_aprob')
                                ->label('Número de Acta')
                                ->required(),

                            Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones Técnicas')
                                ->rows(3)
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
}

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Solicitud')->sortable(),
                Tables\Columns\TextColumn::make('asociado')->label('Cédula')->searchable(),
                Tables\Columns\TextColumn::make('vlr_solicitud')->label('Valor')->money('COP')->sortable(),
                Tables\Columns\TextColumn::make('fecha_solicitud')->label('Fecha Solicitud')->date()->sortable(),
            ])
            ->actions([
                        Tables\Actions\EditAction::make()
                            ->label('Gestionar')
                            ->icon('heroicon-m-pencil-square')
                            ->color('primary'),
                    ])
                    ->headerActions([])
                    ->bulkActions([])
                    ->modifyQueryUsing(fn(Builder $query) => $query->where('estado', 'P'));
            }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCreditoSolicituds::route('/'),
            'view' => Pages\ViewCreditoSolicitud::route('/{record}'),
            'edit' => Pages\EditCreditoSolicitud::route('/{record}/edit'),
        ];
    }
}
