<?php

namespace App\Filament\Clusters\Tesoreria\Resources;

use App\Filament\Clusters\Tesoreria;
use App\Filament\Clusters\Tesoreria\Resources\PagoEncabezadoResource\Pages;
use App\Filament\Clusters\Tesoreria\Resources\PagoEncabezadoResource\RelationManagers;
use App\Filament\Clusters\TesoreriaConsultas;
use App\Models\PagoEncabezado;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PagoEncabezadoResource extends Resource
{
    protected static ?string $model = PagoEncabezado::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-circle';
    protected static ?string $navigationLabel = 'Pagos Realizados';
    protected static ?string $cluster = TesoreriaConsultas::class;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Pago')
                    ->schema([
                        Forms\Components\TextInput::make('nro_docto')->label('Nro Documento')->disabled(),
                        Forms\Components\TextInput::make('cliente')->disabled(),
                        Forms\Components\DatePicker::make('fecha_docto')->label('Fecha')->disabled(),
                        Forms\Components\TextInput::make('estado')->label('Estado Actual')->disabled(),
                    ])->columns(2),

                Forms\Components\Section::make('Detalle de Movimientos')
                    ->schema([
                        Forms\Components\Repeater::make('detalles')
                            ->relationship() // Esta es la clave para ver los detalles
                            ->schema([
                                Forms\Components\TextInput::make('tipo_pago')->columnSpan(1),
                                Forms\Components\TextInput::make('vlr_pago')->numeric()->prefix('$'),
                                Forms\Components\TextInput::make('nro_docto_dvt')->label('Documento Afectado'),
                            ])
                            ->addable(false) // Deshabilitar edición si es solo consulta
                            ->deletable(false)
                            ->columns(3)
                    ])
            ]);
    }

        public static function table(Table $table): Table
        {
            return $table
                ->columns([
                    Tables\Columns\TextColumn::make('fecha_docto')->date()->sortable(),
                    Tables\Columns\TextColumn::make('nro_docto')->searchable()->label('Recibo #'),
                    Tables\Columns\TextColumn::make('cliente')->searchable(),
                    Tables\Columns\TextColumn::make('vlr_pago_efectivo')->money('COP')->label('Efectivo'),
                ])
                ->filters([
                    Tables\Filters\SelectFilter::make('estado')
                        ->options(['A' => 'Activo', 'C' => 'Cerrado']),
                    Tables\Filters\Filter::make('fecha_docto')
                        ->form([
                            Forms\Components\DatePicker::make('desde'),
                            Forms\Components\DatePicker::make('hasta'),
                        ])
                ]);
        }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPagoEncabezados::route('/'),
            'create' => Pages\CreatePagoEncabezado::route('/create'),
            'edit' => Pages\EditPagoEncabezado::route('/{record}/edit'),
        ];
    }
}
