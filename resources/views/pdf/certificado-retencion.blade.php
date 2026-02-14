<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Certificado de Retención en la Fuente</title>
    <style>
        @page {
            size: A4;
            margin: 50px;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            margin: 0;
            padding: 0;
            position: relative;
            height: 100%;
        }
        .container {
            padding: 40px;
            box-sizing: border-box;
            height: calc(100% - 80px);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header .firma {
            text-align: left;
        }
        .header .logo {
            margin-bottom: 10px;
        }
        .header .title {
            font-size: 16px;
            text-transform: uppercase;
            font-weight: bold;
        }
        .header .subtitle {
            font-size: 12px;
            margin-top: 4px;
        }
        .section {
            margin-bottom: 10px;
        }
        .datos, .valores {
            width: 100%;
        }
        .datos p, .intro p {
            margin: 2px 0;
        }
        .intro {
            margin: 10px 0;
            text-align: justify;
        }
        .tabla {
            width: 100%;
            border-collapse: collapse;
            margin-top: 5px;
        }
        .tabla th, .tabla td {
            border: 1px solid #000;
            padding: 4px;
            text-align: center;
        }
        .firma {
            margin-top: 20px;
        }
        .firma p {
            margin: 2px 0;
        }
        .footer {
            position: absolute;
            bottom: 20px;
            left: 40px;
            right: 40px;
            font-size: 10px;
            text-align: center;
        }

        .footer p {
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <div class="firma">
            <img src="{{ public_path('images/Icons.png') }}" alt="Logo" style="height:80px;">
        </div>
        <div class="subtitle"><strong>FONDO NACIONAL DE EMPLEADOS, TRABAJADORES Y PENSIONADOS DEL SECTOR POSTAL</strong></div>
        <div class="subtitle"><strong>DE LAS COMUNICACIONES, ENTIDADES AFINES Y COMPLEMENTARIAS</strong></div>
        <div class="subtitle">FONDEP - NIT 800.090.375-3</div>
        <div class="subtitle"><strong>CERTIFICADO DE RETENCIÓN EN LA FUENTE</strong></div>
        <div class="subtitle">AÑO GRAVABLE 2024</div>
    </div>


    <div class="section intro">
        <p>Certificamos que FONDEP identificado con NIT: 800.090.375-3, efectuó Retención en la Fuente a
            título de Renta, durante el periodo de Enero 01 a Diciembre 31 de 2024, a:</p>
    </div>

    {{-- Datos del asociado --}}
<div class="section datos">
    <table class="datos-tabla">
        <tr>
            <td>
                <strong>ASOCIADO:</strong>
                {{ $tercero->nombres }} {{ $tercero->primer_apellido }} {{ $tercero->segundo_apellido }}
            </td>
            <td>
                <strong>NIT/CÉDULA:</strong>
                {{ $tercero->tercero_id }}
            </td>
        </tr>
        <tr>
            {{-- Segunda Fila --}}
            <td>
                <strong>DIRECCIÓN:</strong>
                {{ $tercero->direccion }}
            </td>
            <td>
                <strong>TELÉFONO:</strong>
                {{ $tercero->telefono }}
            </td>
        </tr>
    </table>
</div>

    <table class="tabla">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Concepto</th>
                <th>Base Retencion</th>
                <th>Retenido</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($certsaldos as $saldo)
                <tr>
                    <td>31 Diciembre 2024</td>
                    <td>Intereses</td>
                    <td>${{ number_format($saldo->base_retencion, 0, ',', '.') }}</td>
                    <td>${{ number_format($saldo->interes, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="tabla" style="margin-top: 20px;">
        <thead>
            <tr>
                <th>Concepto</th>
                <th>Saldo a Corte 31 Diciembre 2024</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Aportes</td>
                <td>${{ number_format($certsaldos->sum('aportes'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Ahorros</td>
                <td>${{ number_format($certsaldos->sum('ahorro'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>Cartera</td>
                <td>${{ number_format($certsaldos->sum('cartera'), 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td>CDAT</td>
                <td>${{ number_format($certsaldos->sum('cdat'), 0, ',', '.') }}</td>
            </tr>
        </tbody>
    </table>


@if($certsaldos->isNotEmpty() && $certsaldos->first()->tipo === 'proveedor')
    <style>
        .table-fin {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 12px;
        }
        .table-fin th, .table-fin td {
            border: 1px solid #000;
            padding: 6px 8px;
        }
        .table-fin th {
            background: #f3f4f6;
            text-align: left;
        }
        .text-right { text-align: right; }
        .section-title td {
            font-weight: 600;
            background: #fafafa;
        }
    </style>

    @php
        $fmt = fn($v) => '$' . number_format($v, 0, ',', '.');

        // Sección 1: Base / Retención (3 columnas)
        $sec1 = [
            ['label' => 'Servicios',  'base' => 'ingresos_servicios',  'ret' => 'servicios'],
            ['label' => 'servicios', 'base' => 'ingresos_servicios', 'ret' => 'servicios'],
            ['label' => 'Arriendos',  'base' => 'ingresos_arriendos',  'ret' => 'arriendos'],
        ];

        $sec2 = [
            ['label' => 'Ingresos por Salario', 'field' => 'ingresos_salarios'],
            ['label' => 'Ingresos por Transporte', 'field' => 'ingresos_transporte'],
            ['label' => 'Deuda a Favor',        'field' => 'deuda_a_favor'],
            ['label' => 'Compras',              'field' => 'compras'],
            ['label' => 'ReteICA',              'field' => 'rte_ica'],
        ];

        $hasSec2 = collect($sec2)->contains(fn($r) => $certsaldos->sum($r['field']) > 0);
    @endphp

    <table class="table-fin">
        <colgroup>
            <col style="width: 50%">
            <col style="width: 25%">
            <col style="width: 25%">
        </colgroup>

        <thead>
            <tr>
                <th>Concepto</th>
                <th class="text-right">Base</th>
                <th class="text-right">Retención</th>
            </tr>
        </thead>

        <tbody>
            {{-- Sección 1: Base / Retención --}}
            @foreach($sec1 as $row)
                @php
                    $base = $certsaldos->sum($row['base']);
                    $ret  = $certsaldos->sum($row['ret']);
                @endphp
                @if($base > 0 || $ret > 0)
                    <tr>
                        <td>{{ $row['label'] }}</td>
                        <td class="text-right">{{ $fmt($base) }}</td>
                        <td class="text-right">{{ $fmt($ret) }}</td>
                    </tr>
                @endif
            @endforeach

            {{-- Sección 2: Concepto / Valor (Valor ocupa 2 columnas para cerrar la grilla) --}}
            @if($hasSec2)
                @foreach($sec2 as $i => $row)
                    @php $val = $certsaldos->sum($row['field']); @endphp
                    @if($val > 0)
                        <tr @if($row['field'] === 'rte_ica') style="border-top: 2px solid #000;" @endif>
                            <td>{{ $row['label'] }}</td>
                            <td class="text-right" colspan="2">{{ $fmt($val) }}</td>
                        </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
    </table>
@endif


    {{-- Notas --}}
    <div class="section intro">
        <p>Los valores retenidos fueron declarados y consignados oportunamente a la Dirección de Impuestos y Aduanas Nacionales.</p>
        <br>
        <p>Este certificado no requiere para su validez firma autógrafa de acuerdo con el artículo 10 del Decreto 836 de 1991.</p>
    </div>
    <br>
    {{-- Firma --}}
    <div class="firma">
        <p><strong>JAIRO BEJARANO WALLENS</strong></p>
        <p>Contador Público - TP No. 5001-T</p>
    </div>

    <br>

    {{-- Pie de página --}}
    <div class="footer">
        <p>Este certificado se expide en Bogotá D.C..</p>
        <p>Calle 24 D bis No. 73 C 48 - Teléfonos: 5481317, 2950229 - Telefax: 2633733</p>
        <p>Bogotá, D.C. - Colombia</p>
    </div>
</div>
</body>
</html>
