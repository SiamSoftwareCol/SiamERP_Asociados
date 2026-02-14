<?php

namespace App\Filament\Exports;

use App\Models\F9083;
use App\Models\InformeF9083;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9083Exporter extends Exporter
{
    protected static ?string $model = F9083::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('numero_credito'),
            ExportColumn::make('tipo_documento'),
            ExportColumn::make('numero_documento'),
            ExportColumn::make('saldo_capital'),
            ExportColumn::make('saldo_vencido'),
            ExportColumn::make('provision_constituida'),
            ExportColumn::make('cupo_otorperdida_esperadaado'),
            ExportColumn::make('dias_mora'),
            ExportColumn::make('calificacion_riesgo'),
            ExportColumn::make('fecha_corte'),
            ExportColumn::make('fecha_reporte'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9083 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
