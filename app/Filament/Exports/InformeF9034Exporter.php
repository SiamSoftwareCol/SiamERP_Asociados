<?php

namespace App\Filament\Exports;

use App\Models\F9034;
use App\Models\InformeF9034;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9034Exporter extends Exporter
{
    protected static ?string $model = F9034::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('tipo_documento'),
            ExportColumn::make('numero_documento'),
            ExportColumn::make('nombre_directivo'),
            ExportColumn::make('cargo'),
            ExportColumn::make('viaticos'),
            ExportColumn::make('otros_pagos'),
            ExportColumn::make('total_pagado'),
            ExportColumn::make('fecha_corte'),
            ExportColumn::make('fecha_reporte'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9034 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
