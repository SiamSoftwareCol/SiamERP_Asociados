<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Comprobante {{ $n_documento }}</title>
    <style>
        @page { margin: 0.8cm; }
        body { font-family: 'Helvetica', sans-serif; font-size: 10px; color: #333; line-height: 1.2; }
        .header-table { width: 100%; border-bottom: 2px solid #000; margin-bottom: 10px; }
        .logo { width: 80px; }
        .title { text-align: center; font-size: 14px; font-weight: bold; text-transform: uppercase; }

        .info-grid { width: 100%; border: 1px solid #ccc; margin-bottom: 10px; border-spacing: 0; }
        .info-grid td { padding: 4px; border: 0.5px solid #eee; }
        .label { font-weight: bold; background-color: #f5f5f5; width: 20%; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .data-table th { background-color: #444; color: white; padding: 6px; text-transform: uppercase; font-size: 9px; }
        .data-table td { border: 1px solid #ccc; padding: 5px; }
        .text-right { text-align: right; }
        .font-mono { font-family: 'Courier', monospace; }

        .totals-row { background-color: #eee; font-weight: bold; font-size: 10px; }

        .signature-container { width: 100%; margin-top: 20px; }
        .signature-box { border: 1px solid #000; height: 50px; text-align: center; vertical-align: bottom; padding-bottom: 5px; width: 25%; font-size: 8px; }
    </style>
</head>
<body>
    <table class="header-table">
        <tr>
            <td width="20%"><img src="{{ public_path('images/Icons1.png') }}" class="logo"></td>
            <td class="title">Comprobante de Contabilidad</td>
            <td width="20%" class="text-right">No. <strong>{{ $n_documento }}</strong></td>
        </tr>
    </table>

    <table class="info-grid">
        <tr>
            <td class="label">Fecha:</td><td>{{ \Carbon\Carbon::parse($fecha_comprobante)->format('d/m/Y') }}</td>
            <td class="label">Tipo Doc:</td><td>{{ $tipoDocumentoContable }}</td>
        </tr>
        <tr>
            <td class="label">Tercero:</td><td colspan="3">{{ $tercero }}</td>
        </tr>
        <tr>
            <td class="label">Concepto:</td><td colspan="3">{{ $descripcion_comprobante }}</td>
        </tr>
    </table>

    <table class="data-table">
        <thead>
            <tr>
                <th>Cuenta</th>
                <th>Nombre de Cuenta</th>
                <th>Detalle</th>
                <th>Débito</th>
                <th>Crédito</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($comprobanteLinea as $linea)
            <tr>
                <td width="10%" class="font-mono">{{ $linea->puc->puc }}</td>
                <td width="30%">{{ substr($linea->puc->descripcion, 0, 40) }}</td>
                <td width="30%">{{ $linea->descripcion_linea }}</td>
                <td width="15%" class="text-right font-mono">{{ number_format($linea->debito, 2) }}</td>
                <td width="15%" class="text-right font-mono">{{ number_format($linea->credito, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="totals-row">
                <td colspan="3" class="text-right">SUMAS IGUALES:</td>
                <td class="text-right font-mono">$ {{ number_format($comprobanteLinea->sum('debito'), 2) }}</td>
                <td class="text-right font-mono">$ {{ number_format($comprobanteLinea->sum('credito'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <table class="signature-container" cellspacing="10">
        <tr>
            <td class="signature-box">PREPARADO<br><strong>{{ strtoupper(Auth::user()->name) }}</strong></td>
            <td class="signature-box">REVISADO</td>
            <td class="signature-box">APROBADO</td>
            <td class="signature-box">RECIBÍ CONFORME (FIRMA/C.C)</td>
        </tr>
    </table>
</body>
</html>
