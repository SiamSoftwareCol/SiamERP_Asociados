<div x-show="tab === 'creditos'">
    <table class="w-full text-sm text-left border rounded-xl overflow-hidden">
        <thead class="bg-gray-50 text-gray-700 uppercase font-bold">
            <tr>
                <th class="px-4 py-3">Documento</th>
                <th class="px-4 py-3 text-right">Saldo Actual</th>
                <th class="px-4 py-3 text-right">Valor Aplicado</th>
                <th class="px-4 py-3 text-center">Acción</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($this->creditosVigentes as $credito)
                <tr class="hover:bg-primary-50/30 transition-colors">
                    <td class="px-4 py-3 font-bold text-gray-900">{{ $credito->nro_docto }}</td>
                    <td class="px-4 py-3 text-right text-gray-600">
                        {{ number_format($credito->vlr_saldo_actual, 2) }}
                    </td>
                    <td class="px-4 py-3 text-right">
                        @if(isset($this->pagos_creditos[$credito->nro_docto]) && $this->pagos_creditos[$credito->nro_docto] > 0)
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                $ {{ number_format($this->pagos_creditos[$credito->nro_docto], 2) }}
                            </span>
                        @else
                            <span class="text-gray-300">$ 0.00</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center">
                        <x-filament::button
                            size="sm"
                            color="info"
                            icon="heroicon-m-calculator"
                            outlined
                            :disabled="$this->valor_total_recibido <= 0"
                            @click="
                                $wire.set('nro_docto_actual', '{{ $credito->nro_docto }}');
                                $dispatch('open-modal', { id: 'modal-liquidacion' });
                            ">
                            Aplicar Abono
                        </x-filament::button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-10 text-center text-gray-400 italic">
                        No se encontraron créditos activos con saldo mayor a cero.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
