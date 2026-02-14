<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Informe de Activos Fijos</title>
    <style>
        @page { margin: 1cm; }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            font-size: 11px;
            color: #333;
            line-height: 1.4;
        }

        /* Encabezado */
        .header-container { width: 100%; margin-bottom: 20px; border-bottom: 2px solid #0f55a0; padding-bottom: 10px; }
        .title { font-size: 20px; color: #2d3e50; font-weight: bold; margin: 0; text-transform: uppercase; }
        .subtitle { font-size: 12px; color: #1d6e74; margin: 5px 0 0 0; }

        /* Estilos de Tabla */
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; table-layout: fixed; }
        th {
            background-color: #2d3e50;
            color: white;
            font-weight: bold;
            text-align: left;
            padding: 10px 8px;
            text-transform: uppercase;
            font-size: 10px;
        }
        td { padding: 8px; border-bottom: 1px solid #ecf0f1; word-wrap: break-word; }

        /* Segmentación por Categoría */
        .category-row {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #2c3e50;
            font-size: 13px;
            border-left: 5px solid #3498db;
        }
        .category-label { padding: 10px; background: #3e76ad; }

        /* Estados con Color */
        .badge {
            padding: 3px 7px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .bueno { background-color: #d4edda; color: #155724; }
        .regular { background-color: #fff3cd; color: #856404; }
        .malo { background-color: #f8d7da; color: #721c24; }

        /* Totales */
        .total-section { text-align: right; font-size: 12px; font-weight: bold; margin-top: -20px; margin-bottom: 20px; padding-right: 8px; color: #2d3e50; }

        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 9px; color: #95a5a6; }
    </style>
</head>
<body>
    <div class="header-container">
        <table style="border: none; margin: 0;">
            <tr>
                <td style="border: none; padding: 0;">
                    <h1 class="title">Informe de Activos Fijos</h1>
                    <p class="subtitle">Reporte detallado por segmentación de categorías</p>
                </td>
                <td style="border: none; padding: 0; text-align: right; vertical-align: middle;">
                    <strong>Fecha:</strong> {{ now()->format('d/m/Y') }}<br>
                    <strong>Hora:</strong> {{ now()->format('h:i A') }}
                </td>
            </tr>
        </table>
    </div>

    @php $granTotal = 0; @endphp

    @foreach($grupos as $categoria => $activos)
        @php $subTotal = 0; @endphp
        <table>
            <thead>
                <tr>
                    <th colspan="6" class="category-label"> Categoría: {{ $categoria }}</th>
                </tr>
                <tr>
                    <th width="15%">Código</th>
                    <th width="35%">Nombre del Activo</th>
                    <th width="12%">Estado</th>
                    <th width="18%">Ubicación</th>
                    <th width="20%">Valor Adq.</th>
                </tr>
            </thead>
            <tbody>
                @foreach($activos as $activo)
                    @php
                        $subTotal += $activo->valor_adquisicion;
                        $granTotal += $activo->valor_adquisicion;
                    @endphp
                    <tr>
                        <td><strong>{{ $activo->codigo }}</strong></td>
                        <td>{{ $activo->nombre }}</td>
                        <td>
                            <span class="badge {{ $activo->estado }}">
                                {{ $activo->estado }}
                            </span>
                        </td>
                        <td>{{ $activo->ubicacion ?? 'No asignada' }}</td>
                        <td style="text-align: right;">$ {{ number_format($activo->valor_adquisicion, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total-section">
            Subtotal {{ $categoria }}: $ {{ number_format($subTotal, 2) }}
        </div>
    @endforeach

    <div style="margin-top: 40px; border-top: 2px solid #2d3e50; padding-top: 10px; text-align: right;">
        <h2 style="margin: 0;">TOTAL GENERAL: $ {{ number_format($granTotal, 2) }}</h2>
    </div>

    <div class="footer">
        Página generada por el Sistema de Gestión de Activos - {{ config('app.name') }}
    </div>
</body>
</html>
