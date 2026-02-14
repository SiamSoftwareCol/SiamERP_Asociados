<?php

namespace App\Filament\Exports;

use App\Models\F9013;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9013Exporter extends Exporter
{
    protected static ?string $model = F9013::class;

    public static function getColumns(): array
    {
        return [
        ExportColumn::make('tipo_identificacion')
            ->label('Tipo de identificación'),
        ExportColumn::make('numero_identificacion')
            ->label('Número de identificación'),
        ExportColumn::make('saldo_fecha')
            ->label('Saldo a fecha'),
        ExportColumn::make('valor_aporte_mensual')
            ->label('Valor Aporte/Contribución Mensual'),
        ExportColumn::make('aporte_ordinario')
            ->label('Aporte/Contribución Ordinario'),
        ExportColumn::make('aporte_extraordinario')
            ->label('Aporte/Contribución Extraordinario'),
        ExportColumn::make('valor_revalorizacion')
            ->label('Valor de Revalorización'),
        ExportColumn::make('monto_promedio')
            ->label('Monto Promedio'),
        ExportColumn::make('ultima_fecha')
            ->label('Última Fecha'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Tu informe F_9013 se ha exportado correctamente ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' No existen registros para la fecha seleccionada.';
        }

        return $body;
    }
}
