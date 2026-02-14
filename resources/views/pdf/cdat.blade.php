<!DOCTYPE html>
<html lang="es">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>CDAT FONDEP Nº {{ $cdat->numero_cdat }}</title>
    <style>
        @page {
            size: letter landscape;
            /* Tamaño carta horizontal */
            margin: 0.5cm;
        }

        body {
            font-family: 'Times New Roman', Times, serif;
            margin: 0;
            padding: 0;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        /* Contenedor con el color amarillo tenue original */
        .certificate-wrapper {
            width: 10in;
            /* Ajustado para ocupar el ancho del papel landscape */
            height: 7.2in;
            background-color: #fdfdf2;
            /* Amarillo tenue original */
            padding: 25px;
            box-sizing: border-box;
            position: relative;
            margin: auto;
            border: 12px double #004d00;
            /* Marco más grueso y decorativo */
            overflow: hidden;
        }

        /* Marca de Agua: Logo */
        .watermark-logo {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            opacity: 0.08;
            z-index: 0;
        }

        /* Marca de Agua: Texto "TITULO ORIGINAL" */
        .watermark-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-35deg);
            font-size: 80px;
            color: rgba(38, 184, 75, 0.11);
            font-weight: bold;
            white-space: nowrap;
            z-index: 0;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            position: relative;
            z-index: 1;
        }

        td {
            border: 1.5px solid #004d00;
            padding: 5px 8px;
            vertical-align: top;
        }

        .label {
            font-weight: bold;
            font-size: 10px;
            color: #002366;
            display: block;
            text-transform: uppercase;
        }

        .value {
            font-family: 'Courier New', Courier, monospace;
            font-size: 13px;
            color: #000;
            font-weight: bold;
            text-transform: uppercase;
        }

        .no-border td {
            border: none;
        }

        .bg-header {
            background-color: rgba(0, 77, 0, 0.05);
        }

        .header-title h1 {
            font-size: 28px;
            margin: 0;
            letter-spacing: 4px;
        }

        .header-title h2 {
            font-size: 16px;
            margin: 0;
            font-weight: normal;
        }

        .legal-text {
            font-size: 10px;
            text-align: justify;
            margin: 20px 0;
            line-height: 1.3;
            z-index: 1;
            position: relative;
        }

        .signatures {
            margin-top: 60px;
        }

        .signatures td {
            border: none;
            border-top: 2px solid #000;
            text-align: center;
            font-size: 11px;
            padding-top: 8px;
        }

        .spacer {
            border: none !important;
            width: 10%;
        }
    </style>
</head>

<body>
    @php
        $fechaC = \Carbon\Carbon::parse($cdat->fecha_creacion);
        $fechaV = \Carbon\Carbon::parse($cdat->fecha_vencimiento);
    @endphp

    <div class="certificate-wrapper">
        <img src="{{ public_path('images/Icons.png') }}" class="watermark-logo">
        <div class="watermark-text">TITULO ORIGINAL</div>

        <table class="no-border" style="margin-bottom: 15px;">
            <tr>
                <td style="width: 15%;">
                    <img src="{{ public_path('images/Icons.png') }}" style="width: 70px;">
                </td>
                <td style="border: none; text-align: center;" class="header-title">
                    <h1>C.D.A.T.</h1>
                    <h2>CERTIFICADO DE AHORRO A TÉRMINO</h2>
                </td>
                <td style="width: 20%; text-align: right; vertical-align: middle;">
                    <span style="color: #d32f2f; font-weight: bold; font-size: 24px;">Nº {{ $cdat->numero_cdat }}</span>
                </td>
            </tr>
        </table>

        <table>
            <tr class="bg-header">
                <td style="width: 20%;"><span class="label">CIUDAD</span></td>
                <td style="width: 20%;"><span class="label">OFICINA</span></td>
                <td colspan="3" style="text-align: center;"><span class="label">FECHA DE CONSTITUCIÓN</span></td>
                <td colspan="3" style="text-align: center;"><span class="label">FECHA DE VENCIMIENTO</span></td>
            </tr>
            <tr>
                <td><span class="value">BOGOTA D.C.</span></td>
                <td><span class="value">PRINCIPAL</span></td>
                <td style="text-align: center;"><span class="label">DÍA</span><span
                        class="value">{{ $fechaC->format('d') }}</span></td>
                <td style="text-align: center;"><span class="label">MES</span><span
                        class="value">{{ $fechaC->format('m') }}</span></td>
                <td style="text-align: center;"><span class="label">AÑO</span><span
                        class="value">{{ $fechaC->format('Y') }}</span></td>
                <td style="text-align: center;"><span class="label">DÍA</span><span
                        class="value">{{ $fechaV->format('d') }}</span></td>
                <td style="text-align: center;"><span class="label">MES</span><span
                        class="value">{{ $fechaV->format('m') }}</span></td>
                <td style="text-align: center;"><span class="label">AÑO</span><span
                        class="value">{{ $fechaV->format('Y') }}</span></td>
            </tr>
        </table>

        <table style="border-top: none;">
            <tr>
                <td style="width: 70%; border-top: none;">
                    <span class="label">TITULAR</span>
                    <span class="value">{{ $cdat->asociado->tercero->nombres }}
                        {{ $cdat->asociado->tercero->primer_apellido }}
                        {{ $cdat->asociado->tercero->segundo_apellido }}</span>
                </td>
                <td style="border-top: none;">
                    <span class="label">C.C. O NIT.</span>
                    <span class="value">{{ $cdat->asociado->tercero->tercero_id }}</span>
                </td>
            </tr>
            <tr>
                <td><span class="label">OBSERVACIONES</span><span
                        class="value">{{ $cdat->observaciones ?? '' }}</span></td>
                <td><span class="label"></span>{{-- <span class="value">{{ $cdat->estado }}</span> --}}</td>
            </tr>
        </table>

        <table style="border-top: none;">
            <tr>
                <td colspan="2">
                    <span class="label">VALOR EN LETRAS</span>
                    <span class="value" style="font-size: 11px;">
                        {{ \App\Helpers\NumeroALetras::convertir($cdat->valor) }} PESOS M/CTE
                    </span>
                </td>
                <td style="width: 10%; text-align: center;"><span class="label">PLAZO</span><span
                        class="value">{{ $cdat->plazo }}</span></td>
                <td style="width: 12%; text-align: center;"><span class="label">TASA NOM.</span><span
                        class="value">{{ $cdat->tasa_interes }}%</span></td>
                <td style="width: 15%; text-align: center;"><span class="label">PAGO</span><span
                        class="value">{{ str_replace('_', ' ', $cdat->pago_interes) }}</span></td>
                <td style="width: 10%; text-align: center;"><span class="label">TASA E.A.</span><span
                        class="value">{{ $cdat->tasa_ea }}%</span></td>
            </tr>
            <tr>
                <td colspan="2" style="background-color: rgba(0,0,0,0.05); vertical-align: middle; height: 50px;">
                    <span class="label">VALOR $</span>
                    <span class="value"
                        style="font-size: 22px;">${{ number_format($cdat->valor, 0, ',', '.') }}</span>
                </td>
                <td colspan="4" class="legal-text" style="padding: 10px;">
                    El día de vencimiento de este certificado o de sus intereses, <strong>Fondep</strong> pagará el
                    valor correspondiente al titular, mediante solicitud escrita en la oficina de expedición.
                    Es entendido que el pago del capital e intereses queda sujeto a la reglamentación contenida en el adjunto de este título.
                </td>
            </tr>
        </table>


        <br><br><br><br><br><br><br><br>

        <table class="signatures">
            <tr>
                <td>FIRMA AUTORIZADA Y SELLO</td>
                <td class="spacer"></td>
                <td>FIRMA AUTORIZADA Y SELLO</td>
            </tr>
        </table>
    </div>

</body>

</html>
