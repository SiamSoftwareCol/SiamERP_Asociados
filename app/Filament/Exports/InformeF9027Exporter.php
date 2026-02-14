<?php

namespace App\Filament\Exports;

use App\Models\F9027;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9027Exporter extends Exporter
{
    protected static ?string $model = F9027::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('tipo_iden'),
            ExportColumn::make('nit'),
            ExportColumn::make('nro_credito'),
            ExportColumn::make('codigo_contable'),
            ExportColumn::make('linea_cred_entidad'),
            ExportColumn::make('fecha_desembolso_inicial'),
            ExportColumn::make('fecha_vencimiento'),
            ExportColumn::make('fecha_ultimo_pago'),
            ExportColumn::make('morosidad'),
            ExportColumn::make('valor_prestamo'),
            ExportColumn::make('valor_cuota_fija'),
            ExportColumn::make('saldo_capital'),
            ExportColumn::make('saldo_intereses'),
            ExportColumn::make('otros_saldos'),
            ExportColumn::make('valor_mora'),
            ExportColumn::make('valor_cuotas_extra'),
            ExportColumn::make('meses_cuota_extra'),
            ExportColumn::make('aportes_sociales'),
            ExportColumn::make('tasa_interes_efectiva'),
            ExportColumn::make('tipo_cuota'),
            ExportColumn::make('altura_cuota'),
            ExportColumn::make('amortizacion'),
            ExportColumn::make('amorti_capital'),
            ExportColumn::make('modalidad'),
            ExportColumn::make('destino_credito'),
            ExportColumn::make('num_modificaciones'),
            ExportColumn::make('modificaciones_al_credito'),
            ExportColumn::make('estado_credito'),
            ExportColumn::make('garantia'),
            ExportColumn::make('clase_garantia'),
            ExportColumn::make('fecha_avaluo'),
            ExportColumn::make('deterioro'),
            ExportColumn::make('deterioro_interes'),
            ExportColumn::make('contingencia'),
            ExportColumn::make('ent_otorgarant'),
            ExportColumn::make('tarj_cred_cupo_rot'),
            ExportColumn::make('tipo_vivienda'),
            ExportColumn::make('vis'),
            ExportColumn::make('rango_tipo'),
            ExportColumn::make('entidad_redescuento'),
            ExportColumn::make('margen_redescuento'),
            ExportColumn::make('subsidio'),
            ExportColumn::make('desembolso'),
            ExportColumn::make('moneda'),
            ExportColumn::make('cod_oficina'),
            ExportColumn::make('nit_patronal'),
            ExportColumn::make('nombre_patronal'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9027 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
