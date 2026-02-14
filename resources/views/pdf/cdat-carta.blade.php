<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Carta de Bienvenida - CDAT {{ $cdat->numero_cdat }}</title>
    <style>
        @page {
            size: letter;
            margin: 0.8cm 1.5cm;
        }
        body {
            font-family: 'Times New Roman', Times, serif;
            font-size: 11pt;
            color: #000000;
            line-height: 1.5;
            margin: 0;
        }
        header { width: 100%; margin-bottom: 8px; }
        .logo { width: 95px; float: left; }
        .header-letter { text-align: right; float: right; font-size: 11pt; }

        .content { clear: both; margin-top: 11px; }

        .saludo {
            text-align: left;
            margin: 15px 0;
            display: block;
        }

        .highlight-box {
            border: 1px solid #000;
            background-color: #f9f9f9;
            padding: 15px;
            margin: 15px 0;
        }
        .highlight-box table { width: 100%; border-collapse: collapse; }
        .highlight-box td { font-size: 10pt; padding: 2px; }

        .signature-section { margin-top: 15px; line-height: 1.1; }

        /* REGLAMENTO EXACTO */
        .conditions-section {
            font-size: 8.2pt;
            color: #333;
            border-top: 1px solid #000;
            margin-top: 25px;
            padding-top: 8px;
            text-align: justify;
        }
        .conditions-title {
            text-align: center;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 8px;
            font-size: 8.5pt;
        }
        .conditions-text {
            column-count: 2;
            column-gap: 20px;
        }
        .conditions-text p { margin: 0 0 5px 0; }

        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10pt;
            border-top: 0.5px solid #ccc;
        }
    </style>
</head>
<body>
    @php \Carbon\Carbon::setLocale('es'); @endphp

    <header>
        <img src="{{ public_path('images/Icons.png') }}" class="logo">
        <div class="header-letter">
            <strong>Bogotá D.C., {{ now()->translatedFormat('d \d\e F \d\e Y') }}</strong><br>
            Referencia: Apertura Certificado de Ahorro a Término
        </div>
    </header>

    <div class="content">
        <div class="saludo">
            Estimado(a) asociado(a),<br>
            <strong>{{ $cdat->asociado?->tercero?->nombre_completo }}</strong>
        </div>

        <p>Reciba un cordial saludo de parte de <strong>FONDEP</strong>.</p>

        <p>Agradecemos su confianza en nuestra entidad, al elegirnos para constituir un nuevo <strong>Certificado de Ahorro a Término (CDAT)</strong>. Esta inversión no solamente fortalece su patrimonio personal y la opción de ahorro e inversión más segura y confiable del mercado, sino que contribuye al desarrollo solidario de nuestra comunidad.</p>

        <div class="highlight-box">
            <table>
                <tr>
                    <td>Título N°: <strong>00{{ $cdat->numero_cdat }}</strong></td>
                    <td>Capital Invertido: <strong>${{ number_format($cdat->valor, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <td>Tasa: <strong>{{ $cdat->tasa_ea }}% E.A.</strong></td>
                    <td>Fecha de Vencimiento: <strong>{{ $cdat->fecha_vencimiento->format('d/m/Y') }}</strong></td>
                </tr>
            </table>
        </div>


    </div>

    <div class="conditions-section">
        <div class="conditions-title">REGLAMENTACIÓN Y CONDICIONES DEL TÍTULO</div>
        <div class="conditions-text">
            <p>1. Si en la fecha pactada para la redención del presente certificado el titular no cobrara dentro de los siguientes cinco (5) días a su vencimiento, se entenderá que este depósito se prorrogará automáticamente por un plazo igual al inicialmente pactado y el interés será el establecido por <strong>FONDEP</strong> en la fecha de su vencimiento.</p>
            <p>2. La devolución del depósito se hará con carta firmada por el titular solicitando la liquidación del CDAT. En el caso que en la fecha de su vencimiento el titular quiera reinvertir parcial o totalmente, <strong>FONDEP</strong> deberá liquidar el certificado y luego constituir un nuevo depósito por el valor que el socio desee, siempre y cuando no sea inferior al depósito mínimo establecido.</p>
            <p>3. El titular debe cumplir con el plazo para recibir la rentabilidad fijada. Si un Asociado necesita liquidar antes su CDAT, <strong>FONDEP</strong> aplicará la disminución de la rentabilidad pactada.</p>
            <p>4. <strong>FONDEP</strong>, sólo reconocerá el certificado que tenga suscritas dos (2) firmas autorizadas de sus funcionario.</p>
            <p>5. Además, se aplicarán las disposiciones legales que sobre estos depósitos estén reglamentado.</p>
            <p>6. El presente certificado solo se pagará al Asociado titular o a un tercero con autorización escrita y autenticada del asociado.</p>
            <p>7. En caso de fallecimiento del asociado será pagado a los beneficiarios de acuerdo a la ley vigente.</p>
            <p>8. El titular declara que ha leído el presente reglamento, que <strong>FONDEP</strong> lo ha informado de manera clara y completa las características del producto, sus condiciones, los procedimientos y seguridades, los derechos y obligaciones.</p>
        </div>
    </div>

        <p>Adjunto a esta comunicación encontrará el título con las condiciones pactadas por la ley, le recordamos que este documento es nominativo y esencial para cualquier trámite futuro relacionado con su inversión.</p>

        <div class="signature-section">
            Atentamente,<br><br><br>
            <br><br><br><br>
            <strong>GUSTAVO MOSQUERA</strong><br>
            GERENTE
        </div>


    <footer>
        FONDEP | Su amigo solidario.
    </footer>
</body>
</html>
