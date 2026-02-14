<?php

namespace App\Filament\Resources\CreditoSolicitudResource\Pages;

use App\Filament\Resources\CreditoSolicitudResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class EditCreditoSolicitud extends EditRecord
{

protected static string $resource = CreditoSolicitudResource::class;


    protected function getSaveFormAction(): \Filament\Actions\Action
    {
        return parent::getSaveFormAction()
            ->label('Aprobar Solicitud')
            ->color('primary')
            ->icon('heroicon-m-check-circle');
    }


    protected function mutateFormDataBeforeSave(array $data): array
    {
        unset($data['lineaCredito']);
        unset($data['empresaCredito']);

        $data['estado'] = 'A';
        $data['lista_desembolso'] = 'S';
        $data['vlr_aprobado'] = $this->record->vlr_solicitud;
        $data['fecha_novedad'] = now()->format('Y-m-d');

        return $data;
    }


    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Crédito Aprobado')
            ->body('La solicitud ha sido aprobada y enviada a lista de desembolso.');
    }


    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('ver_garantias')
                ->label('Verificar Garantías')
                ->icon('heroicon-o-shield-check')
                ->color('primary')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Cerrar')
                ->modalHeading('Garantías de la Solicitud')
                ->modalContent(function ($record) {
                    $garantias = $record->garantias;

                    if ($garantias->isEmpty()) {
                        return view('filament.components.empty-state', [
                            'message' => 'No hay garantías registradas para esta solicitud.'
                        ]);
                    }
                    return view('filament.modals.ver-garantias-lista', [
                        'garantias' => $garantias,
                    ]);
                }),

            Actions\Action::make('Preliquidacion')
                ->label('Ver Preliquidación')
                ->icon('heroicon-o-document-text')
                ->color('primary')
                ->modalHeading('Preliquidación del Crédito')
                ->modalWidth('4xl')
                ->modalSubmitAction(false)
                ->modalContent(function ($record) {
                    $tdocto = 'PLI';


                    $cartera = DB::table('cartera_encabezados')
                        ->where('tdocto', $tdocto)
                        ->where(function($query) use ($record) {
                            $query->where('nro_docto', $record->nro_docto)
                                ->orWhere('cliente', $record->asociado);
                        })
                        ->orderBy('id', 'desc')
                        ->first();

                    if (!$cartera) {
                        return view('filament.components.empty-state', [
                            'message' => 'No se encontró una preliquidación generada.'
                        ]);
                    }

                    $detalles = DB::table('cuotas_detalles')
                        ->where('tdocto', $tdocto)
                        ->where('nro_docto', $cartera->nro_docto)
                        ->get();


                    $codigosConceptos = $detalles->pluck('con_descuento')->unique();
                    $nombresConceptos = DB::table('concepto_descuentos')
                        ->whereIn('codigo_descuento', $codigosConceptos)
                        ->pluck('descripcion', 'codigo_descuento');


                    $detallesAgrupados = [];
                    foreach ($detalles as $det) {
                        $nombre = $nombresConceptos[$det->con_descuento] ?? $det->con_descuento;
                        $detallesAgrupados[$det->nro_cuota][$nombre] = $det->vlr_detalle;
                    }


                    $conceptos = collect($detallesAgrupados)->flatMap(function($cuota) {
                        return array_keys($cuota);
                    })->unique()->values();


                    return view('filament.modals.preliquidacion', [
                        'cartera'           => $cartera,
                        'cuotas'            => DB::table('cuotas_encabezados')
                                                ->where('tdocto', $tdocto)
                                                ->where('nro_docto', $cartera->nro_docto)
                                                ->orderBy('nro_cuota', 'asc')
                                                ->get(),
                        'conceptos'         => $conceptos,
                        'detallesAgrupados' => $detallesAgrupados,
                    ]);
                }),




        ];
    }
}
