<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\ActivosFijos;
use App\Models\Activo;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Collection;

class ReporteActivoResource extends Resource
{
    protected static ?string $model = Activo::class;
    protected static ?string $cluster = ActivosFijos::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationLabel = 'Informe de Activos';
    protected static ?string $modelLabel = 'Informe de Activos';

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código'),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre del activo'),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'bueno' => 'primary',
                        'regular' => 'warning',
                        'malo' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('valor_adquisicion')
                    ->label('Valor')
                    ->money('COP'),
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación'),
            ])
            ->headerActions([
                Tables\Actions\Action::make('exportExcel')
                    ->label('Exportar Excel')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('primary')
                    ->action(function () {
                        return Excel::download(new class implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings {
                            public function collection()
                            {
                                return Activo::with('categoria')->get()->map(fn($a) => [
                                    $a->codigo,
                                    $a->nombre,
                                    $a->categoria?->nombre,
                                    $a->estado,
                                    $a->valor_adquisicion,
                                    $a->fecha_adquisicion
                                ]);
                            }
                            public function headings(): array
                            {
                                return ['Código', 'Nombre', 'Categoría', 'Estado', 'Valor', 'Fecha'];
                            }
                        }, 'informe-activos-' . now()->format('Y-m-d') . '.xlsx');
                    }),

                Tables\Actions\Action::make('exportPDF')
                    ->label('Generar PDF')
                    ->icon('heroicon-o-document-text')
                    ->color('primary')
                    ->action(function () {
                        $activosAgrupados = \App\Models\Activo::with(['categoria', 'responsable'])
                            ->get()
                            ->groupBy(fn($item) => $item->categoria->nombre ?? 'SIN CATEGORÍA');

                        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.reporte-activos', [
                            'grupos' => $activosAgrupados
                        ]);

                        $pdf->setPaper('letter', 'portrait');

                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->output();
                        }, 'informe-activos-' . now()->format('Y-m-d') . '.pdf');
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => \App\Filament\Resources\ReporteActivoResource\Pages\ListReporteActivos::route('/'),
        ];
    }
}
