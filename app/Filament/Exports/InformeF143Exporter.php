<?php

namespace App\Filament\Exports;

use App\Models\F143;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF143Exporter extends Exporter
{
    protected static ?string $model = F143::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('unidad_captura'),
            ExportColumn::make('codigo_renglon'),
            ExportColumn::make('descripcion_renglon'),
            ExportColumn::make('numero'),
            ExportColumn::make('porcentaje'),
            ExportColumn::make('recursos_girados_benef_asociados'),
            ExportColumn::make('numero_asociados_beneficiados'),
            ExportColumn::make('recursos_girados_benef_empleados'),
            ExportColumn::make('numero_empleados_beneficiados'),
            ExportColumn::make('recursos_girados_benef_comunidad'),
            ExportColumn::make('numero_personas_comunidad_beneficiadas'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f143 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
