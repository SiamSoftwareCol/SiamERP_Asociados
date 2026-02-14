<?php

namespace App\Filament\Clusters\ReportesSuperSolidaria\Resources;

use App\Filament\Clusters\ReportesSuperSolidaria;
use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9999Resource\Pages;
use App\Filament\Clusters\ReportesSuperSolidaria\Resources\F9999Resource\RelationManagers;
use App\Filament\Exports\InformeF9999Exporter;
use App\Models\F9999;
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

class F9999Resource extends Resource
{
    protected static ?string $model = F9999::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $cluster = ReportesSuperSolidaria::class;
    protected static ?string $navigationLabel = 'Asociados y empleados - F_9999';
    protected static ?string $modelLabel = 'F_9999 - Asociados y empleados';
    protected static ?int $navigationSort = 20;

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
            ->heading('F9999 - Asociados, empleados y deudores por venta de bienes y servicios')
            ->description('Información consolidada sobre número de asociados, empleados y deudores por venta de bienes o servicios.')
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
                        $fechaCorte = Carbon::parse($data['fecha_corte'])->format('Y-m-d');
                        DB::statement("SELECT public.formato_f9999(?)", [
                            $fechaCorte,
                        ]);
                        session()->put('vista_actualizada', true);
                        Notification::make()
                            ->title('La consulta de información se ha actualizado correctamente.')
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
                    ->exporter(InformeF9999Exporter::class)
                    ->fileName(fn (Export $export): string => "Formato_F_9999_-{$export->getKey()}.csv")
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
            'index' => Pages\ListF9999S::route('/'),
            'create' => Pages\CreateF9999::route('/create'),
            'edit' => Pages\EditF9999::route('/{record}/edit'),
        ];
    }
}
