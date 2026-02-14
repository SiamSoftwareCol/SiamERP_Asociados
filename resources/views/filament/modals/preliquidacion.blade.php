<style>
@media print {
    /* Ocultar interfaz completa de Filament y scrollbars */
    body *, .fi-modal-window, .fi-modal-overlay, .fi-topbar, .fi-sidebar {
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Mostrar solo el área de contenido */
    #area-imprimible, #area-imprimible * {
        visibility: visible !important;
    }

    #area-imprimible {
        position: absolute !important;
        left: 0 !important;
        top: 0 !important;
        width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    /* Ajuste de escala para que 36 cuotas quepan en una hoja */
    .tabla-compacta {
        font-size: 8.5px !important;
    }

    .tabla-compacta td, .tabla-compacta th {
        padding-top: 2px !important;
        padding-bottom: 2px !important;
        line-height: 1 !important;
    }

    @page {
        size: letter portrait;
        margin: 0.8cm;
    }
}
</style>

<div id="area-imprimible" class="p-4 space-y-4 bg-white text-gray-900 print:p-0">
    <div class="flex justify-between items-end border-b pb-2">
        <div>
            <h1 class="text-lg font-bold text-primary-600">PLAN DE AMORTIZACIÓN</h1>
            <p class="text-md text-gray-500 uppercase">{{ $cartera->tdocto }} No. {{ $cartera->nro_docto }}</p>
        </div>
        <div class="text-right text-md">
            <p><strong>Total Crédito:</strong> ${{ number_format($cartera->vlr_docto_vto, 0, ',', '.') }}</p>
        </div>
    </div>

    <div class="overflow-x-auto border rounded-lg">
        <table class="min-w-full divide-y divide-gray-200 text-[10px] tabla-compacta">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-2 py-2 text-center font-bold text-gray-700 italic border-r">No.</th>
                    <th class="px-2 py-2 text-center font-bold text-gray-700 italic border-r">Vencimiento</th>
                    @foreach($conceptos as $nombre)
                        <th class="px-2 py-2 text-center font-bold text-gray-700 italic border-r uppercase">{{ $nombre }}</th>
                    @endforeach
                    <th class="px-2 py-2 text-center font-bold text-blue-800 bg-blue-50">Total Cuota</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @foreach ($cuotas as $c)
                    <tr class="hover:bg-gray-50">
                        <td class="px-2 py-1 font-bold text-gray-500 text-center border-r">{{ $c->nro_cuota }}</td>
                        <td class="px-2 py-1 font-mono text-center border-r">{{ \Carbon\Carbon::parse($c->fecha_vencimiento)->format('d/m/Y') }}</td>
                        @foreach($conceptos as $concepto)
                            @php $valorConcepto = $detallesAgrupados[$c->nro_cuota][$concepto] ?? 0; @endphp
                            <td class="px-2 py-1 text-right text-gray-600 border-r">
                                {{ $valorConcepto > 0 ? '$'.number_format($valorConcepto, 0, ',', '.') : '-' }}
                            </td>
                        @endforeach
                        <td class="px-2 py-1 text-right font-bold text-blue-900 bg-blue-50/50">
                            ${{ number_format($c->vlr_cuota, 0, ',', '.') }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-100 font-bold border-t-2">
                <tr>
                    <td colspan="2" class="px-2 py-1 text-right uppercase border-r">Totales:</td>
                    @foreach($conceptos as $concepto)
                        <td class="px-2 py-1 text-right border-r">
                            ${{ number_format(collect($detallesAgrupados)->sum($concepto), 0, ',', '.') }}
                        </td>
                    @endforeach
                    <td class="px-2 py-1 text-right text-blue-900 bg-blue-50">
                        ${{ number_format($cuotas->sum('vlr_cuota'), 0, ',', '.') }}
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="p-2 bg-gray-50 border border-gray-200 text-[8px] text-gray-500 italic rounded">
        * Proyección informativa sujeta a cambios según fecha de desembolso y firma de pagaré.
    </div>
</div>
