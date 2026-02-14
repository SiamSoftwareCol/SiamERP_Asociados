<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GestionAsociadoResource\Pages;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\AportesRelationManager;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\CertificadoDepositosRelationManager;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\CobranzasRelationManager;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\CreditoSolicitudesRelationManager;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\CuotasRelationManager;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\GarantiasRelationManager;
use App\Filament\Resources\GestionAsociadoResource\RelationManagers\ObligacionesRelationManager;
use App\Models\Asociado;
use Filament\Forms\Form;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GestionAsociadoResource extends Resource
{
    protected static ?string $model = Asociado::class;
    protected static ?string $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Estado de Cuenta';
    protected static ?string $navigationGroup = 'Gestión de Asociados';
    protected static ?string $modelLabel = 'Estado de cuenta Asociado';
    protected static ?string $pluralModelLabel = 'Estado de cuenta Asociado';
    protected static ?int       $navigationSort = 1;



    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('estado_cliente_id', '!=', 3);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
                Tables\Columns\TextColumn::make('tercero.tercero_id')
                    ->searchable()
                    ->label('Identificacion'),
                Tables\Columns\TextColumn::make('tercero.nombres')
                    ->searchable()
                    ->label('Nombres'),
                Tables\Columns\TextColumn::make('tercero.primer_apellido')
                    ->searchable()
                    ->label('Primer Apellido'),
                Tables\Columns\TextColumn::make('tercero.segundo_apellido')
                    ->searchable()
                    ->label('Segundo Apellido'),
                Tables\Columns\TextColumn::make('EstadoCliente.nombre')
                    ->searchable()
                    ->label('Estado Actual')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Activo' => 'success',
                        default => 'warning',
                    }),
                Tables\Columns\TextColumn::make('pagaduria.nombre')
                    ->searchable()
                    ->label('Pagaduria'),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make()->label('Gestionar'),
            ])
            ->bulkActions([])
            ->paginated([5, 10, 25, 50, 100, 'all'])
            ->defaultPaginationPageOption(5);
    }

    public static function getRelations(): array
    {
        return [
            //
            CreditoSolicitudesRelationManager::class,
            CuotasRelationManager::class,
            ObligacionesRelationManager::class,
            AportesRelationManager::class,
            CertificadoDepositosRelationManager::class,
            GarantiasRelationManager::class,
            CobranzasRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGestionAsociados::route('/'),
            'create' => Pages\CreateGestionAsociado::route('/create'),
            'edit' => Pages\EditGestionAsociado::route('/{record}/edit'),
            'view' => Pages\ViewAsociado::route('/{record}'),
        ];
    }
}
