<?php

namespace App\Filament\Exports;

use App\Models\F9081;
use App\Models\InformeF9081;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9081Exporter extends Exporter
{
    protected static ?string $model = F9081::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('tipo_iden'),
            ExportColumn::make('nit'),
            ExportColumn::make('nro_credito'),
            ExportColumn::make('garantia2'),
            ExportColumn::make('fecha_avaluo2'),
            ExportColumn::make('clase_garantia2'),
            ExportColumn::make('cupo_otorgado'),
            ExportColumn::make('calif_eval_cartera'),
            ExportColumn::make('cuotas_pactadas'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9081 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
