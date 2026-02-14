<?php

namespace App\Filament\Exports;

use App\Models\F8888;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF8888Exporter extends Exporter
{
    protected static ?string $model = F8888::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id_tipo_directivo'),
            ExportColumn::make('tipo_iden'),
            ExportColumn::make('nit'),
            ExportColumn::make('nombre_cr'),
            ExportColumn::make('identificación'),
            ExportColumn::make('principal_suplente'),
            ExportColumn::make('empleado_socio'),
            ExportColumn::make('fecha_nombra'),
            ExportColumn::make('fecha_posesion'),
            ExportColumn::make('periodo_vigencia'),
            ExportColumn::make('parentescos'),
            ExportColumn::make('vinculadas'),
            ExportColumn::make('empresa_revisor_fiscal'),
            ExportColumn::make('tarjeta_prof_revisor_fiscal'),
            ExportColumn::make('tipo_tarjeta'),
            ExportColumn::make('num_certificado'),
            ExportColumn::make('fecha_expe_certi'),
            ExportColumn::make('tipo_iddili'),
            ExportColumn::make('nit_diligencia'),
            ExportColumn::make('cargo_diligencia'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f8888 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
