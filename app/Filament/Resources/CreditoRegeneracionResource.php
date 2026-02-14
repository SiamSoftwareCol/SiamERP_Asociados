<?php
namespace App\Filament\Resources;

use App\Filament\Clusters\ParametrosCartera;
use App\Models\CreditoSolicitud;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CreditoRegeneracionResource extends Resource
{
    protected static ?string $model = CreditoSolicitud::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-path';
    protected static ?string $slug = 'credito-regeneracion';
    protected static ?string $cluster = ParametrosCartera::class;
    protected static ?string $navigationLabel = 'Regenerar Planes';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Crédito')
                    ->columns(3)
                    ->schema([
                        Forms\Components\Placeholder::make('solicitud')->content(fn($record) => $record->solicitud),
                        Forms\Components\Placeholder::make('asociado')->label('Cédula')->content(fn($record) => $record->asociado),
                        Forms\Components\Placeholder::make('vlr_solicitud')->label('Valor')->content(fn($record) => number_format($record->vlr_solicitud, 2)),
                    ]),

                Forms\Components\Section::make('Composición de la Cuota (Nuevos Conceptos)')
                    ->description('Agregue conceptos adicionales que se sumarán a la liquidación de cada cuota.')
                    ->schema([
                    Forms\Components\Repeater::make('nuevos_conceptos')
                        ->schema([
                            Forms\Components\Select::make('codigo_descuento')
                                ->label('Concepto')
                                ->options(\Illuminate\Support\Facades\DB::table('concepto_descuentos')
                                    ->pluck('descripcion', 'codigo_descuento'))
                                ->required()
                                ->searchable(),
                            Forms\Components\TextInput::make('valor')
                                ->label('Valor Mensual')
                                ->numeric()
                                ->prefix('$')
                                ->required(),
                        ])
                        ->columns(2)
                        // ESTA ES LA FORMA ACTUALIZADA EN V3:
                        ->addActionLabel('Agregar Concepto Adicional')
                        ->collapsible() // Opcional: permite encoger los conceptos para ver mejor
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn (Builder $query) => $query->where('estado', 'A'))
            ->columns([
                Tables\Columns\TextColumn::make('solicitud')->label('Solicitud'),
                Tables\Columns\TextColumn::make('terceroAsociado.nombre_completo')->label('Cliente'),
                Tables\Columns\TextColumn::make('vlr_solicitud')->label('Valor')->money('COP'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()->label('Reprocesar'),
            ]);
    }


    public static function getPages(): array
        {
            return [
                'index' => \App\Filament\Resources\CreditoRegeneracionResource\Pages\ListCreditoRegeneracions::route('/'),
                'edit' => \App\Filament\Resources\CreditoRegeneracionResource\Pages\EditCreditoRegeneracion::route('/{record}/edit'),
            ];
        }


}
