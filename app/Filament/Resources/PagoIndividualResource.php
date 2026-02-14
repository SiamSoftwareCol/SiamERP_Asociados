<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\Tesoreria;
use App\Filament\Resources\PagoIndividualResource\Pages;
use App\Models\PagoIndividual;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;

class PagoIndividualResource extends Resource
{
    protected static ?string $model = PagoIndividual::class;
    protected static ?string $cluster = Tesoreria::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document';
    protected static ?string $navigationLabel = 'Pagos Individuales';
    protected static ?string $modelLabel = 'Pagos Individuales';
    protected static ?int       $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([]);
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-currency-dollar')
            ->emptyStateHeading('Pagos individuales')
            ->emptyStateDescription(
                'Aún no se han registrado pagos individuales.
             Desde aquí puedes crear y administrar pagos de forma rápida y segura.'
            )
            ->emptyStateActions([
                Action::make('create')
                    ->label('Registrar pago')
                    ->icon('heroicon-m-plus-circle')
                    ->color('primary')
                    ->url(route('filament.admin.tesoreria.resources.pago-individuals.create'))
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
            'index' => Pages\ListPagoIndividuals::route('/'),
            'create' => Pages\CreatePagoIndividual::route('/create'),
            'view' => Pages\ViewPagoIndividual::route('/{record}'),
            'edit' => Pages\EditPagoIndividual::route('/{record}/edit'),
        ];
    }
}
