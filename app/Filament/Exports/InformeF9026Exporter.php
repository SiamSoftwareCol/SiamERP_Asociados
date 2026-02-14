<?php

namespace App\Filament\Exports;

use App\Models\F9026;
use App\Models\InformeF9026;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class InformeF9026Exporter extends Exporter
{
    protected static ?string $model = F9026::class;

    public static function getColumns(): array
    {
        return [

            ExportColumn::make('tipo_iden')
            ->label('TipoIden'),
            ExportColumn::make('nit')
            ->label('NIT'),
            ExportColumn::make('codigo_contable')
            ->label('CodigoContable'),
            ExportColumn::make('nombre_deposito')
            ->label('NombreDeposito'),
            ExportColumn::make('tipo_ahorro')
            ->label('TipoAhorro'),
            ExportColumn::make('amortizacion')
            ->label('Amortizacion'),
            ExportColumn::make('fecha_apertura')
            ->label('FechaApertura'),
            ExportColumn::make('plazo')
            ->label('Plazo'),
            ExportColumn::make('fecha_vencimiento')
            ->label('FechaVencimiento'),
            ExportColumn::make('modalidad')
            ->label('Modalidad'),
            ExportColumn::make('tasa_interes_nominal')
            ->label('TasaInteresNominal'),
            ExportColumn::make('tasa_interes_efectiva')
            ->label('TasaInteresEfectiva'),
            ExportColumn::make('intereses_causados')
            ->label('InteresesCausados'),
            ExportColumn::make('saldo')
            ->label('Saldo'),
            ExportColumn::make('deposito_inicial')
            ->label('DepositoInicial'),
            ExportColumn::make('numero_cuenta')
            ->label('NumeroCuenta'),
            ExportColumn::make('excenta_gmf')
            ->label('excentagmf'),
            ExportColumn::make('fecha_aceptacion_egmf')
            ->label('fechaaceptacionegmf'),
            ExportColumn::make('estado')
            ->label('Estado'),
            ExportColumn::make('cta_bajo_monto')
            ->label('CtaBajoMonto'),
            ExportColumn::make('cotitulares')
            ->label('Cotitulares'),
            ExportColumn::make('conjunta_colectivo')
            ->label('ConjuntaColectivo'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your informe f9026 export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
