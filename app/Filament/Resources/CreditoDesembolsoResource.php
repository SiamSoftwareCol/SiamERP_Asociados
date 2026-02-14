<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CreditoDesembolsoResource\Pages;
use App\Filament\Resources\CreditoDesembolsoResource\RelationManagers;
use App\Models\CreditoSolicitud;
use App\Filament\Clusters\ParametrosCartera;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditoDesembolsoResource extends Resource
{
    protected static ?string $model = CreditoSolicitud::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?int $navigationSort = 3;
    protected static ?string $cluster = ParametrosCartera::class;
    protected static ?string $navigationLabel = 'Desembolsos Pendientes';
    protected static ?string $modelLabel = 'Desembolso';
    protected static ?string $slug = 'cartera/desembolsos';

public static function form(Form $form): Form
{
    return $form
        ->schema([
            Forms\Components\Section::make('Información del Desembolso')
                ->description('Condiciones de la solicitud y condiciones de desembolso.')
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
                            Forms\Components\Select::make('ente_aprobador')
                                ->label('Ente que Aprueba')
                                ->options([
                                    'JUNTA DIRECTIVA' => 'Junta Directiva',
                                    'GERENCIA' => 'Gerencia',
                                    'COMITE DE CREDITO' => 'Comité de Crédito',
                                ])
                                ->required()
                                ->disabled()
                                ->columnSpan(2)
                                ->native(false),
                            Forms\Components\TextInput::make('nro_acta_aprob')
                                ->label('Número de Acta')
                                ->columnSpan(2)
                                ->disabled()
                                ->required(),
                            Forms\Components\Textarea::make('observaciones')
                                ->label('Observaciones Técnicas')
                                ->rows(3)
                                ->disabled()
                                ->columnSpanFull(),
                        ]),
                ]),
        ]);
}

    public static function table(Table $table): Table
        {
            return $table
                ->modifyQueryUsing(fn (Builder $query) =>
                    $query->where('estado', 'A')->where('lista_desembolso', 'S')
                )
                ->columns([
                    Tables\Columns\TextColumn::make('id')->label('Solicitud'),
                    Tables\Columns\TextColumn::make('terceroAsociado.nombre_completo')->label('Cliente'),
                    Tables\Columns\TextColumn::make('vlr_solicitud')->label('Valor')->money('COP'),
                    Tables\Columns\TextColumn::make('nro_acta_aprob')->label('Acta Aprobación'),
                ])
                ->actions([
                    Tables\Actions\EditAction::make()
                        ->label('Procesar Desembolso')
                        ->icon('heroicon-m-check-badge')
                        ->color('primary'),
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
            'index' => Pages\ListCreditoDesembolsos::route('/'),
            'edit' => Pages\EditCreditoDesembolso::route('/{record}/edit'),
        ];
    }
}
