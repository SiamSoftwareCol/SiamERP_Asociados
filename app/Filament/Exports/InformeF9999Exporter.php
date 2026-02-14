<?php

namespace App\Filament\Exports;

use App\Models\F9999;
use App\Models\InformeF9999;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9999Exporter extends Exporter
{
    protected static ?string $model = F9999::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('tipo_identificacion'),
            ExportColumn::make('numero_identificacion'),
            ExportColumn::make('primer_apellido'),
            ExportColumn::make('segundo_apellido'),
            ExportColumn::make('nombres'),
            ExportColumn::make('genero'),
            ExportColumn::make('fecha_nacimiento'),
            ExportColumn::make('estado_civil'),
            ExportColumn::make('telefono'),
            ExportColumn::make('celular'),
            ExportColumn::make('dirección'),
            ExportColumn::make('email'),
            ExportColumn::make('codigo_municipio'),
            ExportColumn::make('estrato'),
            ExportColumn::make('asociado'),
            ExportColumn::make('empleado'),
            ExportColumn::make('activo'),
            ExportColumn::make('fecha_ingreso'),
            ExportColumn::make('fecha_retiro'),
            ExportColumn::make('asistio_ult_asamblea'),
            ExportColumn::make('actividad_economica'),
            ExportColumn::make('ocupacion'),
            ExportColumn::make('sector_economico'),
            ExportColumn::make('tipo_contrato'),
            ExportColumn::make('jornada_laboral'),
            ExportColumn::make('nivel_escolaridad'),
            ExportColumn::make('nivel_ingresos'),
            ExportColumn::make('mujer_cabeza_familia'),

        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9999 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
