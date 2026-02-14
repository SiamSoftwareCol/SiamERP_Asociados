<?php

namespace App\Services;

use App\Models\Configuracion;
use App\Models\ConfiguracionFecha;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class DateService
{
    /**
     * Obtiene la fecha de proceso actual del sistema.
     * Si no hay una fecha configurada, devuelve la fecha real.
     */
    public function get(): Carbon
    {
        // Usamos caché para no consultar la BD en cada llamada
        $fechaGuardada = Cache::rememberForever('fecha_proceso_sistema', function () {
            return ConfiguracionFecha::find('fecha_proceso')?->valor;
        });

        if ($fechaGuardada) {
            return Carbon::parse($fechaGuardada);
        }

        return Carbon::now();
    }

    public function set(Carbon $fecha): void
    {
        ConfiguracionFecha::updateOrCreate(
            ['clave' => 'fecha_proceso'],
            ['valor' => $fecha->toDateString()]
        );

        Cache::forget('fecha_proceso_sistema');
    }


    public function reset(): void
    {
        ConfiguracionFecha::where('clave', 'fecha_proceso')->delete();
        Cache::forget('fecha_proceso_sistema');
    }
}
