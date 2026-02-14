<?php

namespace App\Filament\Clusters\InformeSaldos\Resources;

use App\Filament\Clusters\InformeSaldos;
use App\Filament\Clusters\InformeSaldos\Resources\CarteracorteinfoResource\Pages;
use App\Models\Carteracorteinfo;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use App\Filament\Exports\CarterainfoExporter;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Carbon\Carbon;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;

class CarteracorteinfoResource extends Resource
{
    protected static ?string $model = Carteracorteinfo::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'SaldosCartera Corte Especifico';
    protected static ?string $modelLabel = 'SaldosCartera Corte Especifico';
    protected static ?string $cluster = InformeSaldos::class;

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
            ->heading('Informes Saldos de Aportes y Ahorros')
            ->description('Consulte la sabana de Saldos de Aportes y Ahorros a la fecha.')
            ->paginated(false)
            ->striped()
            ->defaultPaginationPageOption(5)
            ->emptyStateIcon('heroicon-o-cloud-arrow-down')
            ->emptyStateHeading('')
            ->columns([
            ])
            ->headerActions([
                Action::make('Actualizar Vista')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->form([
                        DatePicker::make('fecha_corte')
                            ->required()
                            ->maxDate(now()->format('Y-m'))
                            ->placeholder('Seleccione el año y mes')
                            ->displayFormat('F Y')
                    ])
                    ->modalWidth('sm')
                    ->modalHeading('Actualizar Consulta')
                    ->modalDescription('¿Fecha de Corte de la informacion?')
                    ->modalSubmitActionLabel('Generar')
                    ->modalIcon('heroicon-o-cloud-arrow-down')
                    ->action(function (array $data): void {
                        $fechaCorte = Carbon::parse($data['fecha_corte']);
                        DB::statement("SELECT public.crear_vista_carteracorteinfo(?, ?)", [
                                $fechaCorte->format('Y'),
                                (string) $fechaCorte->month,
                            ]);
                    session()->put('vista_actualizada', true);
                        Notification::make()
                            ->title('La consulta de informacion se ha actualizado correctamente.')
                            ->success()
                            ->send();
                    })
                    ->label('Consulta Informacion'),
                ExportAction::make()
                    ->color('secondary')
                    ->modalWidth('sm')
                    ->modalHeading('Que columnas del informe desea exportar?')
                    ->modalIcon('heroicon-o-cloud-arrow-down')
                    ->modalSubmitActionLabel('Exportar')
                    ->visible(fn () => session()->get('vista_actualizada', false))
                    ->exporter(CarterainfoExporter::class)
                    ->fileName(fn (Export $export): string => "Saldos_Cartera_-{$export->getKey()}.csv")
                    ->label('Descargar Informe')
                    ->after(function () {
                        session()->forget('vista_actualizada');
                    }),
            ]);
    }



    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCarteracorteinfos::route('/'),
            'create' => Pages\CreateCarteracorteinfo::route('/create'),
            'edit' => Pages\EditCarteracorteinfo::route('/{record}/edit'),
        ];
    }
}
