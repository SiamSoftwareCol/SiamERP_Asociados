<x-filament-panels::page>
    <x-filament-panels::form>
        {{ $this->form }}

        <x-filament::button wire:loading.class="pointer-events-none opacity-70 end" wire:click="updateRecord">
            Actualizar datos
        </x-filament::button>
    </x-filament-panels::form>
</x-filament-panels::page>
