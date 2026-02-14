@php
    use App\Models\Firma;
    $firmantes = Firma::first();
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 4px; text-align: left; }
        .table th { background-color: #f0f0f0; }
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        .bg-gray { background-color: #f0f0f0; }
        .header-company { font-size: 14px; }
        .header-title { font-size: 12px; }
        .header-subtitle { font-size: 10px; }
        .no-border { border: none !important; }
        .group-header { font-size: 11px !important; border-bottom: 1px solid #999; }
        .group-footer { border-bottom: 2px solid #555; }
    </style>
</head>
<body>
    {{-- ENCABEZADO GENERAL DEL REPORTE --}}
    <table class="table">
        <tr>
            <td colspan="4" class="text-center header-company"><strong>{{ $nombre_compania }}</strong></td>
        </tr>
        <tr>
            <td colspan="4" class="text-center header-title"><strong>NIT: {{ $nit }}</strong></td>
        </tr>
        <tr>
            <td colspan="4" class="text-center header-title"><strong>{{ $titulo }}</strong></td>
        </tr>
        <tr>
            <td colspan="4" class="text-center header-subtitle">
                Desde {{ $fecha_inicial->format('d/m/Y') }} hasta {{ $fecha_final->format('d/m/Y') }}
            </td>
        </tr>
        <tr>
             <td><strong>Fecha Impresión:</strong></td>
             <td>{{ now()->format('d/m/Y h:i A') }}</td>
             <td><strong>Usuario:</strong></td>
             <td>{{ auth()->user()->name ?? 'N/A' }}</td>
        </tr>
    </table>

    <br>

    {{-- CUERPO DEL REPORTE --}}
    <table class="table">
        @switch($tipo_balance)

            @case('auxiliar_tercero')
                <thead>
                    <tr class="bg-gray">
                        <th><strong>Fecha</strong></th>
                        <th><strong>Documento</strong></th>
                        <th><strong>Detalle</strong></th>
                        <th class="text-right"><strong>Débito</strong></th>
                        <th class="text-right"><strong>Crédito</strong></th>
                        <th class="text-right"><strong>Saldo</strong></th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="bg-gray">
                        <td colspan="6" class="group-header">
                            <strong>TERCERO: {{ $tercero->tercero }} - {{ $tercero->tercero_nombre }} {{ $tercero->primer_apellido }}</strong>
                        </td>
                    </tr>
                    <tr><td colspan="6" class="no-border" style="height: 5px;"></td></tr>

                    @foreach ($cuentas as $puc => $cuenta)
                        <tr class="bg-gray">
                            <td colspan="3" class="group-header">
                                <strong>CUENTA: {{ $puc }} - {{ $cuenta['descripcion'] }}</strong>
                            </td>
                            <td colspan="3" class="text-right group-header">
                                <strong>SALDO ANTERIOR: {{ number_format($cuenta['saldo_inicial_grupo'], 2) }}</strong>
                            </td>
                        </tr>

                        @forelse ($cuenta['movimientos'] as $movimiento)
                            <tr>
                                <td>{{ $movimiento->fecha }}</td>
                                <td>{{ $movimiento->documento }} {{ $movimiento->n_documento }}</td>
                                <td>{{ $movimiento->descripcion_linea }}</td>
                                <td class="text-right">{{ number_format($movimiento->debito, 2) }}</td>
                                <td class="text-right">{{ number_format($movimiento->credito, 2) }}</td>
                                <td class="text-right">{{ number_format($movimiento->saldo_nuevo, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No hay movimientos para esta cuenta.</td></tr>
                        @endforelse

                        @php
                            $totalDebitos = collect($cuenta['movimientos'])->sum('debito');
                            $totalCreditos = collect($cuenta['movimientos'])->sum('credito');
                            $nuevoSaldo = $cuenta['saldo_inicial_grupo'] + $totalDebitos - $totalCreditos;
                        @endphp
                        <tr class="bg-gray group-footer">
                            <td colspan="3"><strong>TOTALES CUENTA {{ $puc }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalDebitos, 2) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalCreditos, 2) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($nuevoSaldo, 2) }}</strong></td>
                        </tr>
                        <tr><td colspan="6" class="no-border" style="height: 10px;"></td></tr>
                    @endforeach
                </tbody>
                @break

            @case('auxiliar_cuentas')
            @case('auxiliar_cuentas_detalles')
                 <thead>
                    <tr class="bg-gray">
                        <th><strong>Fecha</strong></th>
                        <th><strong>Documento</strong></th>
                        <th><strong>Detalle</strong></th>
                        <th class="text-right"><strong>Débito</strong></th>
                        <th class="text-right"><strong>Crédito</strong></th>
                        <th class="text-right"><strong>Saldo</strong></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($cuentas as $puc => $cuenta)
                        <tr class="bg-gray">
                            <td colspan="3" class="group-header">
                                <strong>CUENTA: {{ $puc }} - {{ $cuenta['descripcion'] }}</strong>
                            </td>
                            <td colspan="3" class="text-right group-header">
                                <strong>SALDO ANTERIOR: {{ number_format($cuenta['saldo_inicial_grupo'], 2) }}</strong>
                            </td>
                        </tr>

                        @forelse ($cuenta['movimientos'] as $movimiento)
                            <tr>
                                <td>{{ $movimiento->fecha }}</td>
                                <td>{{ $movimiento->documento }} {{ $movimiento->n_documento }}</td>
                                <td>{{ $movimiento->descripcion_linea }}</td>
                                <td class="text-right">{{ number_format($movimiento->debito, 2) }}</td>
                                <td class="text-right">{{ number_format($movimiento->credito, 2) }}</td>
                                <td class="text-right">{{ number_format($movimiento->saldo_nuevo, 2) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center">No hay movimientos para esta cuenta.</td></tr>
                        @endforelse

                        @php
                            $totalDebitos = collect($cuenta['movimientos'])->sum('debito');
                            $totalCreditos = collect($cuenta['movimientos'])->sum('credito');
                            $nuevoSaldo = $cuenta['saldo_inicial_grupo'] + $totalDebitos - $totalCreditos;
                        @endphp
                        <tr class="bg-gray group-footer">
                            <td colspan="3"><strong>TOTALES CUENTA {{ $puc }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalDebitos, 2) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($totalCreditos, 2) }}</strong></td>
                            <td class="text-right"><strong>{{ number_format($nuevoSaldo, 2) }}</strong></td>
                        </tr>
                        <tr><td colspan="6" class="no-border" style="height: 10px;"></td></tr>
                    @endforeach
                </tbody>
                @break

        @endswitch
    </table>
</body>
</html>
