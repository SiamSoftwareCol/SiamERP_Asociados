<?php

namespace App\Filament\Asociado\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = null;
    protected static bool $isLazy = false;
    protected static ?int $sort = 3;

    public static function canView(): bool
    {
        return auth()->user()->canview === 'asociados' && auth()->user()->asociado_id !== null;
    }

    protected function getStats(): array
    {
        $user = auth()->user();

        if (!$user->asociado) {
            return [];
        }

        $data = DB::select("select * from obtener_datos_cliente(?)", [$user->asociado->codigo_interno_pag]);

        if (empty($data)) {
            return [
                Stat::make('Información', 'Sin datos disponibles')->color('gray')
            ];
        }

        $res = $data[0];

        return [
            Stat::make('Saldo actual', number_format($res->total_saldo_actual, 2). ' COP')
                ->description('Aquí está tu saldo actual de cartera')
                ->color('primary'),
            Stat::make('Créditos', $res->total_registros)
                ->description('Aquí tendrás el número de créditos activos')
                ->color('primary'),
            Stat::make('Saldo de ahorros', number_format($res->total_ahorros, 2). ' COP')
                ->description('Aquí está tu saldo actual de ahorros')
                ->color('primary'),
        ];
    }
}
