<?php

namespace App\Filament\Resources\CreditoDesembolsoResource\Pages;

use App\Filament\Resources\CreditoDesembolsoResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class EditCreditoDesembolso extends EditRecord
{
    protected static string $resource = CreditoDesembolsoResource::class;

    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Confirmar y Desembolsar')
            ->color('primary');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        DB::transaction(function () {
            $solicitud = $this->record;

            // 1. Obtener el plan de desembolso vinculado por el campo 'solicitud'
            $plan = DB::table('plan_desembolsos')
                ->where('solicitud_id', $solicitud->solicitud)
                ->first();

            if (!$plan) {
                throw new \Exception("No se encontró un plan de desembolso para esta solicitud.");
            }

            $nroPli = $plan->nro_documento_vto_enc;

            // 2. Clonar el registro en CARTERA_ENCABEZADOS de PLI a PAG
            $encabezadoPli = DB::table('cartera_encabezados')
                ->where('tdocto', 'PLI')
                ->where('nro_docto', $nroPli)
                ->first();

            if (!$encabezadoPli) {
                throw new \Exception("No se encontró el encabezado de cartera PLI.");
            }

            $nuevoEncabezado = (array) $encabezadoPli;
            unset($nuevoEncabezado['id']); // Eliminamos el ID para que cree uno nuevo

            $nuevoEncabezado['tdocto'] = 'PAG';
            $nuevoEncabezado['fecha_desembolso'] = now()->format('Y-m-d');
            $nuevoEncabezado['usuario_crea'] = auth()->user()->name;
            $nuevoEncabezado['created_at'] = now();
            $nuevoEncabezado['updated_at'] = now();

            // Insertamos y capturamos el nuevo nro_docto si es necesario
            // Si nro_docto no es autoincremental y debe ser el mismo, se mantiene del array original
            $idNuevoEncabezado = DB::table('cartera_encabezados')->insertGetId($nuevoEncabezado);

            // NOTA: Si tu nro_docto es igual al ID autoincremental, usamos $idNuevoEncabezado
            // Si nro_docto es un consecutivo manual que debe ser igual al PLI, usamos $nroPli
            $nroPag = $nroPli;

            // 3. Función para clonar registros (Cuotas y Detalles)
            $clonarRegistros = function ($tabla, $nroDoctoOrigen, $nroDoctoDestino) {
                $registros = DB::table($tabla)
                    ->where('tdocto', 'PLI')
                    ->where('nro_docto', $nroDoctoOrigen)
                    ->get();

                if ($registros->isNotEmpty()) {
                    $nuevosRegistros = $registros->map(function ($item) use ($nroDoctoDestino) {
                        $itemArray = (array) $item;
                        unset($itemArray['id']);
                        $itemArray['tdocto']     = 'PAG';
                        $itemArray['nro_docto']  = $nroDoctoDestino; // Aseguramos el vínculo al nuevo encabezado
                        $itemArray['created_at'] = now();
                        $itemArray['updated_at'] = now();
                        return $itemArray;
                    })->toArray();

                    DB::table($tabla)->insert($nuevosRegistros);
                }
            };

            // 4. Ejecutar clonación de sub-tablas
            $clonarRegistros('cuotas_encabezados', $nroPli, $nroPag);
            $clonarRegistros('cuotas_detalles', $nroPli, $nroPag);

            // 5. Actualizar el plan de desembolso para que apunte al nuevo documento PAG
            DB::table('plan_desembolsos')
                ->where('id', $plan->id)
                ->update([
                    'tipo_documento_enc' => 'PAG',
                    'nro_documento_vto_enc' => $nroPag
                ]);

            // 6. Actualizar el estado de la solicitud
            $solicitud->update([
                'estado'           => 'M', // Monto Desembolsado
                'lista_desembolso' => 'N',
                'fecha_novedad'    => now()->format('Y-m-d'),
            ]);
        });

        return $data;
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Desembolso Exitoso')
            ->body('Se ha creado el encabezado PAG y se han clonado las cuotas y detalles correctamente.');
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
