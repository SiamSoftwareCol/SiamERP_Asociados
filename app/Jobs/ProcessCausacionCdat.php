<?php

namespace App\Jobs;

use App\Models\CertificadoDeposito;
use App\Models\LogProcesoCdat;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessCausacionCdat implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected Carbon $fechaCorte;
    protected User $user;

    public function __construct(string $fechaCorte, User $user)
    {
        $this->fechaCorte = Carbon::parse($fechaCorte);
        $this->user = $user;
    }

    public function handle(): void
    {
        $log = LogProcesoCdat::create([
            'tipo_proceso' => 'CAUSACION_INTERES',
            'periodo_proceso' => $this->fechaCorte,
            'user_id' => $this->user->id,
            'estado' => 'INICIADO',
        ]);

        try {
            DB::beginTransaction();

            $cdatsActivos = CertificadoDeposito::where('estado', 'A')
                                  ->whereDate('fecha_creacion', '<=', $this->fechaCorte)
                                  ->get();

            $procesados = 0;
            foreach ($cdatsActivos as $cdat) {
                $this->causarInteresIndividual($cdat);
                $procesados++;
            }

            $log->update([
                'estado' => 'COMPLETADO',
                'detalles' => ['mensaje' => "Se procesaron {$procesados} CDATs."]
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

    private function causarInteresIndividual(CertificadoDeposito $cdat): void
    {
        // Encuentra la última fecha de causación o usa la fecha de creación del CDAT
        $ultimaCausacion = $cdat->causaciones()->latest('periodo_hasta')->first();
        $fechaDesde = $ultimaCausacion ? Carbon::parse($ultimaCausacion->periodo_hasta)->addDay() : Carbon::parse($cdat->fecha_creacion);

        // No causar si la fecha de inicio es posterior a la fecha de corte
        if ($fechaDesde->greaterThan($this->fechaCorte)) {
            return;
        }

        $dias = $fechaDesde->diffInDays($this->fechaCorte) + 1;
        // Fórmula de Interés Simple Diario: (Capital * Tasa Anual / 100) / 365 * Días
        $interesBruto = ($cdat->valor * ($cdat->tasa_ea / 100) / 365) * $dias;
        // Asumiendo un porcentaje de retención guardado en el CDAT
        $valorRetencion = $interesBruto * ($cdat->retencion / 100);

        $cdat->causaciones()->create([
            'fecha_causacion' => $this->fechaCorte,
            'periodo_desde' => $fechaDesde,
            'periodo_hasta' => $this->fechaCorte,
            'dias_liquidados' => $dias,
            'valor_interes_bruto' => round($interesBruto, 2),
            'valor_retencion' => round($valorRetencion, 2),
            'estado' => 'CAUSADO',
        ]);
    }
}
