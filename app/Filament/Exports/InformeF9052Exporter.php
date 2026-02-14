<?php

namespace App\Filament\Exports;

use App\Models\F9052;
use App\Models\InformeF9052;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9052Exporter extends Exporter
{
    protected static ?string $model = F9052::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9052 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
