<x-filament-panels::page>

    <form wire:submit="simular">
        {{ $this->form }}

        <div class="flex items-center gap-x-3 mt-6">
            <x-filament::button type="submit">
                Simular Rendimiento
            </x-filament::button>

            <x-filament::button color="gray" wire:click="limpiar" type="button">
                Limpiar
            </x-filament::button>
        </div>
    </form>

    @if ($resultados)
        <div class="mt-8">
            <x-filament::section>
                <x-slot name="heading">
                    Resultados de la Simulación
                </x-slot>

                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <dt class="text-sm font-medium text-gray-500">Valor Invertido</dt>
                        <dd class="mt-1 text-xl font-semibold text-gray-900">
                            ${{ number_format($resultados['valor_invertido'], 2, ',', '.') }}
                        </dd>
                    </div>

                    <div class="p-4 bg-green-50 rounded-lg">
                        <dt class="text-sm font-medium text-green-600">Intereses Generados</dt>
                        <dd class="mt-1 text-xl font-semibold text-green-800">
                            ${{ number_format($resultados['intereses_brutos'], 2, ',', '.') }}
                        </dd>
                    </div>

                    <div class="p-4 bg-red-50 rounded-lg">
                        <dt class="text-sm font-medium text-red-600">Retención en la Fuente</dt>
                        <dd class="mt-1 text-xl font-semibold text-red-800">
                           - ${{ number_format($resultados['valor_retencion'], 2, ',', '.') }}
                        </dd>
                    </div>

                    <div class="p-4 bg-blue-50 rounded-lg">
                        <dt class="text-sm font-medium text-blue-600">Intereses Netos</dt>
                        <dd class="mt-1 text-xl font-semibold text-blue-800">
                            ${{ number_format($resultados['intereses_netos'], 2, ',', '.') }}
                        </dd>
                    </div>

                    <div class="p-4 bg-indigo-50 rounded-lg col-span-1 md:col-span-2">
                        <dt class="text-sm font-medium text-indigo-600">VALOR TOTAL A RECIBIR AL VENCIMIENTO</dt>
                        <dd class="mt-1 text-3xl font-bold text-indigo-900">
                            ${{ number_format($resultados['valor_final'], 2, ',', '.') }}
                        </dd>
                    </div>
                </dl>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
