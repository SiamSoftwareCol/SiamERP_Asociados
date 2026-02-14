<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ParametrosCartera;
use App\Filament\Resources\CreditoConsultaResource\Pages;
use App\Filament\Resources\CreditoConsultaResource\RelationManagers;
use App\Models\CreditoSolicitud;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CreditoConsultaResource extends Resource
{
    protected static ?string $model = CreditoSolicitud::class;
    protected static ?int $navigationSort = 5;
    protected static ?string $navigationIcon = 'heroicon-o-magnifying-glass-circle';
    protected static ?string $cluster = ParametrosCartera::class;
    protected static ?string $navigationLabel = 'Consulta de Créditos';
    protected static ?string $modelLabel = 'Consulta';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('solicitud')
                    ->label('ID')
                    ->sortable(),

                Tables\Columns\TextColumn::make('terceroAsociado.nombre_completo')
                    ->label('Titular')
                    ->searchable(),

                Tables\Columns\TextColumn::make('vlr_solicitud')
                    ->label('Valor')
                    ->money('COP')
                    ->sortable(),

                // Visualización "Bonita" de los Estados con Colores
                Tables\Columns\TextColumn::make('estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'P' => 'warning', // Pendiente - Amarillo
                        'A' => 'success', // Aprobado - Verde
                        'M' => 'info',    // Desembolsado - Azul
                        'R' => 'danger',  // Rechazado - Rojo
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'P' => 'PENDIENTE',
                        'A' => 'APROBADO',
                        'M' => 'DESEMBOLSADO',
                        'R' => 'RECHAZADO',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('fecha_solicitud')
                    ->label('Fecha')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('estado')
                    ->options([
                        'P' => 'Pendiente',
                        'A' => 'Aprobado',
                        'M' => 'Desembolsado',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make() // Solo ver, no editar
                    ->label('Ver Detalles')
                    ->color('gray'),
            ])
            ->bulkActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => CreditoConsultaResource\Pages\ListCreditoConsultas::route('/'),
        ];
    }
}
