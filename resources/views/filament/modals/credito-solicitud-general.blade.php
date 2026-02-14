<div class="space-y-6 text-sm text-gray-700">
    <x-filament::section heading="Datos Generales">
        <div class="grid grid-cols-2 gap-3">
            <div><strong>Número de Solicitud:</strong> {{ $solicitud->solicitud }}</div>
            <div><strong>Identificacion:</strong> {{ $solicitud->asociado }}</div>
            <div><strong>Línea:</strong> {{ $solicitud->lineaCredito?->descripcion }}</div>
            <div><strong>Periodo de Pago:</strong> {{ $solicitud->periodo_pago }}</div>
            <div><strong>Tasa:</strong> {{ $solicitud->tipo_tasa }}</div>
        </div>
    </x-filament::section>

    <x-filament::section heading="Montos y Condiciones">
        <div class="grid grid-cols-2 gap-3">
            <div><strong>Valor Solicitado:</strong> ${{ number_format($solicitud->vlr_solicitud ?? 0, 0, ',', '.') }}</div>
            <div><strong>Número de Cuotas:</strong> {{ $solicitud->nro_cuotas_max }}</div>
            <div><strong>Cuotas de Gracia:</strong> {{ $solicitud->nro_cuotas_gracia }}</div>
        </div>
    </x-filament::section>
</div>
