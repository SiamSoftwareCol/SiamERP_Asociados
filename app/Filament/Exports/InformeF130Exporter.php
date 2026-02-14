<?php

namespace App\Filament\Exports;

use App\Models\F130;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF130Exporter extends Exporter
{
    protected static ?string $model = F130::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('unidad_captura'),
            ExportColumn::make('codigo_renglon'),
            ExportColumn::make('descripcion_renglon'),
            ExportColumn::make('valor'),
            ExportColumn::make('porcentaje'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Tu informe F_130 se ha exportado y ' . number_format($export->successful_rows) . ' ' . str('filas')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('filas')->plural($failedRowsCount) . ' fallaron al exportar.';
        }

        return $body;
    }
}
