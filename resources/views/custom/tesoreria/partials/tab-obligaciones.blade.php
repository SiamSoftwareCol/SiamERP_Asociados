<div x-show="tab === 'obligaciones'">
    <table class="w-full text-sm text-left border">
        <thead class="bg-gray-50 text-gray-700">
            <tr>
                <th class="px-4 py-2">Concepto</th>
                <th class="px-4 py-2 text-right">Saldo</th>
                <th class="px-4 py-2 text-right">Aplicar Pago</th>
            </tr>
        </thead>
        <tbody>
            @forelse($this->otrasObligaciones as $obli)
                <tr class="border-t">
                    <td class="px-4 py-2">
                        <span class="font-bold text-xs text-gray-400">#{{ $obli->id }}</span>
                        {{ $obli->nombre_concepto ?? 'Obligación Pendiente' }}
                    </td>
                    <td class="px-4 py-2 text-right">
                        {{ number_format($obli->vlr_cuota, 2) }}
                    </td>
                    <td class="px-4 py-2">
                        <x-filament::input.wrapper>
                            <x-filament::input
                                type="number"
                                placeholder="0.00"
                                wire:model.live.debounce.500ms="pagos_obligaciones.{{ $obli->id }}"
                                x-on:change="$wire.sincronizarDistribucion()"
                            />
                        </x-filament::input.wrapper>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="p-4 text-center text-gray-500">Sin otras obligaciones pendientes.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
