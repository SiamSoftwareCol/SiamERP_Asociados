<?php

namespace App\Filament\Exports;

use App\Models\F3;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF3Exporter extends Exporter
{
    protected static ?string $model = F3::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('cuenta'),
            ExportColumn::make('descripcion_cuenta'),
            ExportColumn::make('saldo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Tu informe F_3 se ha exportado y ' . number_format($export->successful_rows) . ' ' . str('filas')->plural($export->successful_rows) . ' exportadas.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= '  ' . number_format($failedRowsCount) . ' ' . str('filas')->plural($failedRowsCount) . ' fallaron al exportar.';
        }

        return $body;
    }
}
