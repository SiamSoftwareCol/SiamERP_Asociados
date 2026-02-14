<?php

namespace App\Filament\Asociado\Resources;

use App\Filament\Asociado\Resources\EstadoCuentaResource\Pages;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\AportesRelationManager;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\CertificadoDepositosRelationManager;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\CobranzasRelationManager;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\CreditoSolicitudesRelationManager;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\CuotasRelationManager;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\GarantiasRelationManager;
use App\Filament\Asociado\Resources\EstadoCuentaResource\RelationManagers\ObligacionesRelationManager;
use App\Models\Asociado;
use App\Models\EstadoCuenta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EstadoCuentaResource extends Resource
{
    protected static ?string $model = Asociado::class;

    protected static bool $shouldRegisterNavigation = false;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Estado de Cuenta';


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
                //
            ])
            ->filters([
                //
            ])
            ->actions([
                //Tables\Actions\ViewAction::make(),
                //Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            //GarantiasRelationManager::class,
            //CobranzasRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            //'index' => Pages\ListEstadoCuentas::route('/'),
            //'create' => Pages\CreateEstadoCuenta::route('/create'),
            'view' => Pages\ViewEstadoCuenta::route('/{record}'),
            //'edit' => Pages\EditEstadoCuenta::route('/{record}/edit'),
        ];
    }
}
