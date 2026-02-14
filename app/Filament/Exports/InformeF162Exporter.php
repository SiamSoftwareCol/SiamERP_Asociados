<?php

namespace App\Filament\Exports;

use App\Models\F162;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF162Exporter extends Exporter
{
    protected static ?string $model = F162::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('unidad_captura'),
            ExportColumn::make('codigo_renglon'),
            ExportColumn::make('descripcion_renglon'),
            ExportColumn::make('saldo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f162 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
