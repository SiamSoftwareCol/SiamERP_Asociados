<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}

        <hr class="my-6">

        @if ($this->show)
            <div x-data="{
                tab: 'creditos',
                nro_docto_actual: null,
                // ... otras funciones de tu objeto pagoIndividual()
            }" x-init="$wire.limpiarDatosEnMemoria()">

                {{-- Botón de acción superior (Guardar todo) --}}
                @include('custom.tesoreria.partials.header-actions')

                {{-- Pestañas de navegación --}}
                @include('custom.tesoreria.partials.tabs-navigation')

                {{-- Contenido de cada pestaña --}}
                <div class="mt-4 bg-white p-6 rounded-xl border shadow-sm">
                    {{-- Usamos x-show para que solo sea visible la pestaña activa --}}
                    <div x-show="tab === 'creditos'">
                        @include('custom.tesoreria.partials.tab-creditos')
                    </div>

                    <div x-show="tab === 'obligaciones'" x-cloak>
                        @include('custom.tesoreria.partials.tab-obligaciones')
                    </div>

                    <div x-show="tab === 'otros'" x-cloak>
                        @include('custom.tesoreria.partials.tab-otros')
                    </div>
                </div>

                <div class="mt-8 flex justify-end p-6 bg-gray-50 border-t rounded-b-xl">
                    <x-filament::button wire:click="generarComprobante" wire:loading.attr="disabled" color="success"
                        icon="heroicon-s-check-circle" size="xl">
                        <span wire:loading.remove wire:target="generarComprobante">
                            Finalizar y Contabilizar Pago
                        </span>
                        <span wire:loading wire:target="generarComprobante">
                            Procesando...
                        </span>
                    </x-filament::button>
                </div>

                {{-- Modales --}}
                @include('custom.tesoreria.partials.modal-liquidacion')
                @include('custom.tesoreria.partials.modal-status')
            </div>
        @endif
    </x-filament-panels::form>
</x-filament-panels::page>
