<x-filament-panels::page>

    <x-filament::section>
        <x-slot name="heading">
            Detalles de mi estado de cuenta
        </x-slot>

        <x-slot name="description">
            Aqui encontraras todo lo relacionado a tu estado de cuenta.
        </x-slot>

        {{-- Content --}}
        {{-- <div class="form-container">
            <div class="form-group">
                <label for="identificacion">Nro. de identificación</label>
                <x-filament::input.wrapper disabled>
                    <x-filament::input id="identificacion" type="text" wire:model="identificacion" disabled />
                </x-filament::input.wrapper>
            </div>

            <div class="form-group">
                <label for="nombre">Nombre de Asociado</label>
                <x-filament::input.wrapper disabled>
                    <x-filament::input id="nombre" type="text" wire:model="nombre" disabled />
                </x-filament::input.wrapper>
            </div>

            <div class="form-group">
                <label for="estado">Estado</label>
                <x-filament::input.wrapper disabled>
                    <x-filament::input id="estado" type="text" wire:model="estado" disabled />
                </x-filament::input.wrapper>
            </div>

            <div class="form-group">
                <label for="pagaduria">Pagaduría</label>
                <x-filament::input.wrapper disabled>
                    <x-filament::input id="pagaduria" type="text" wire:model="pagaduria" disabled />
                </x-filament::input.wrapper>
            </div>
        </div>

        <style>
            .form-container {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                /* Dos columnas de igual tamaño */
                gap: 16px;
                /* Espaciado entre los elementos */
            }

            .form-group {
                display: flex;
                flex-direction: column;
                /* Asegura que el label esté encima del input */
            }

            label {
                font-weight: bold;
                margin-bottom: 8px;
                /* Espaciado entre el label y el input */
            }
        </style> --}}
        {{ $this->infolist }}
    </x-filament::section>

    @if (count($relationManagers = $this->getRelationManagers()))
        <x-filament-panels::resources.relation-managers :active-manager="$this->activeRelationManager" :managers="$relationManagers" :owner-record="$record"
            :page-class="static::class" />
    @endif
</x-filament-panels::page>
