<?php

namespace App\Filament\Resources\CreditoRegeneracionResource\Pages;

use App\Filament\Resources\CreditoRegeneracionResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditCreditoRegeneracion extends EditRecord
{
    protected static string $resource = CreditoRegeneracionResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        DB::transaction(function () use ($data) {
            $solicitud = $this->record;

            // 1. Obtener info del plan actual
            $plan = DB::table('plan_desembolsos')
                ->where('solicitud_id', $solicitud->solicitud)
                ->first();

            if ($plan) {
                $nroCan = $plan->nro_documento_vto_enc;

                // 2. ELIMINAR PRELIQUIDACIÓN ANTERIOR (Solo PLI)
                // Es necesario para no duplicar datos al regenerar
                DB::table('cuotas_detalles')->where('tdocto', 'PLI')->where('nro_docto', $nroCan)->delete();
                DB::table('cuotas_encabezados')->where('tdocto', 'PLI')->where('nro_docto', $nroCan)->delete();

                // 3. REGENERAR ENCABEZADOS
                // (Aquí deberías insertar las cuotas base según el plazo de la solicitud)
                // Ejemplo simplificado de inserción de un nuevo concepto a todas las cuotas:
                foreach ($data['nuevos_conceptos'] as $concepto) {
                    for ($i = 1; $i <= $solicitud->nro_cuotas_max; $i++) {
                        DB::table('cuotas_detalles')->insert([
                            'tdocto' => 'PLI',
                            'nro_docto' => $nroCan,
                            'nro_cuota' => $i,
                            'con_descuento' => $concepto['codigo_descuento'],
                            'vlr_detalle' => $concepto['valor'],
                            'created_at' => now(),
                        ]);
                    }
                }

                // 4. Actualizar totales en cuotas_encabezados si es necesario
                // Aquí podrías llamar a un Store Procedure de SQL:
                // DB::statement("EXEC sp_RecalcularTotalesCredito ?", [$nroCan]);
            }
        });

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Planes Regenerados Exitosamente';
    }
}
