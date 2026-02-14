<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources;

use App\Filament\Clusters\ReportesSuperSolidaria;
use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F3Resource\Pages;
use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F3Resource\RelationManagers;
use App\Filament\Exports\InformeF3Exporter;
use App\Models\F3;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\DatePicker;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ExportAction;
use Carbon\Carbon;
use Filament\Actions\Exports\Models\Export;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class F3Resource extends Resource
{
    protected static ?string $model = F3::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $cluster = ReportesSuperSolidaria::class;
    protected static ?string $navigationLabel = 'Estados financieros - CUC_3';
    protected static ?string $modelLabel = 'CUC_3 - Estados financieros';
    protected static ?int $navigationSort = 1;


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
            ->heading('F3 - Catálogo Único de Cuentas con fines de supervisión')
            ->description('Catálogo Único de Cuentas reportado con fines de supervisión. Permite a la Supersolidaria verificar la situación contable y financiera de la entidad.')
            ->paginated(false)
            ->striped()
            ->defaultPaginationPageOption(5)
            ->emptyStateIcon('heroicon-o-cloud-arrow-down')
            ->emptyStateHeading('')
            ->columns([])
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
                            DB::statement("SELECT public.formato_f3(?, ?)", [
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
                    ->visible(fn() => session()->get('vista_actualizada', false))
                    ->exporter(InformeF3Exporter::class)
                    ->fileName(fn (Export $export): string => "Formato_F_3_-{$export->getKey()}.csv")
                    ->label('Descargar Informe')
                    ->after(function () {
                        session()->forget('vista_actualizada');
                    }),
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
            'index' => Pages\ListF3S::route('/'),
            'create' => Pages\CreateF3::route('/create'),
            'edit' => Pages\EditF3::route('/{record}/edit'),
        ];
    }
}
