<?php

namespace App\Jobs;

use App\Models\CausacionInteresCdat;
use App\Models\LogProcesoCdat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessGeneracionPagosCdat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected User $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function handle(): void
    {
        $log = LogProcesoCdat::create([
            'tipo_proceso' => 'GENERACION_PAGO',
            'periodo_proceso' => now()->format('Y-m-d'),
            'user_id' => $this->user->id,
            'estado' => 'INICIADO',
        ]);

        try {
            DB::beginTransaction();

            $causacionesPendientes = CausacionInteresCdat::where('estado', 'CAUSADO')
                ->whereHas('cdat', fn($query) => $query->where('pago_interes', 'mensual'))
                ->get();

            $pagosGenerados = 0;
            foreach ($causacionesPendientes as $causacion) {
                $valorNeto = $causacion->valor_interes_bruto - $causacion->valor_retencion;

                $pago = $causacion->cdat->pagos()->create([
                    'valor_bruto' => $causacion->valor_interes_bruto,
                    'valor_retencion' => $causacion->valor_retencion,
                    'valor_neto_pagado' => $valorNeto,
                    'fecha_pago' => now(),
                ]);

                $causacion->update([
                    'estado' => 'PAGADO',
                    'pago_interes_cdat_id' => $pago->id,
                ]);
                $pagosGenerados++;
            }

            $log->update([
                'estado' => 'COMPLETADO',
                'detalles' => ['mensaje' => "Se generaron {$pagosGenerados} pagos."]
            ]);
            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            $log->update([
                'estado' => 'FALLIDO',
                'detalles' => ['error' => $e->getMessage()]
            ]);
        }
    }
}
