<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Tesoreria;
use App\Filament\Resources\PagoCarteraResource\Pages;
use App\Filament\Resources\PagoCarteraResource\RelationManagers;
use App\Models\PagoCartera;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class PagoCarteraResource extends Resource
{
    protected static ?string $model = PagoCartera::class;
    protected static ?string $cluster = Tesoreria::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Pagos de Cartera';
    protected static ?string $modelLabel = 'Pagos Cartera';
    protected static ?int       $navigationSort = 1;

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
            ->modifyQueryUsing(fn($query) => $query->whereRaw('1 = 0'))
            ->columns([
                //
            ])
            ->filters([

            ])
            ->actions([
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                ]),
            ])
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateHeading('Pagos de Cartera')
            ->emptyStateDescription(
                'Desde aquí puedes crear y administrar pagos de forma rápida y segura.'
            )
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar pago')
                    ->icon('heroicon-m-plus-circle')
                    ->color('primary')
                    ->url(route('filament.admin.tesoreria.resources.pago-carteras.create'))
                    ->button(),
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
            'index' => Pages\ListPagoCarteras::route('/'),
            'create' => Pages\CreatePagoCartera::route('/create'),
            'edit' => Pages\EditPagoCartera::route('/{record}/edit'),
        ];
    }
}
