<?php

namespace App\Filament\Exports;

use App\Models\F173;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF173Exporter extends Exporter
{
    protected static ?string $model = F173::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('unidad_captura'),
            ExportColumn::make('codigo_renglon'),
            ExportColumn::make('descripcion_renglon'),
            ExportColumn::make('saldo_fecha'),
            ExportColumn::make('porcentaje_fecha'),
            ExportColumn::make('flujos_reales_mes_anterior'),
            ExportColumn::make('dias_1_al_15'),
            ExportColumn::make('porcentaje_dias_1_al_15'),
            ExportColumn::make('dia_16_a_cierre_mes'),
            ExportColumn::make('porcentaje_dia_16_a_cierre'),
            ExportColumn::make('mayor_1_mes_menor_igual_2_meses'),
            ExportColumn::make('porcentaje_mayor_1_mes_menor_igual_2_meses'),
            ExportColumn::make('mayor_2_meses_menor_igual_3_meses'),
            ExportColumn::make('porcentaje_mayor_2_meses_menor_igual_3_meses'),


        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f173 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
