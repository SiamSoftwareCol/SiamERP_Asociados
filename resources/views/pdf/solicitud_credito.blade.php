@php
    use Carbon\Carbon;

    // Asegúrate de que 'fecha_nacimiento' sea un string en formato adecuado y crea el objeto Carbon
    $fechaNacimiento = isset($tercero['fecha_nacimiento'])
        ? Carbon::createFromFormat('Y-m-d', $tercero['fecha_nacimiento'])
        : Carbon::now();

    // Función auxiliar para verificar y formatear números.
    // Usamos el operador de fusión de null (??) para asegurar un valor por defecto.
    $formatNumber = function ($value) {
        return number_format($value ?? 0, 2, ',', '.');
    };

    // Asumimos que el solicitante es el Deudor Solidario 1 para fines de llenado.
    // Corrección para la dirección del Deudor Solidario 1
    $direccionTercero = isset($tercero['direccion']) ? $tercero['direccion'] : '';

@endphp
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Solicitud de Crédito</title>
    <style>
        .sidebar-bleed {
            position: fixed; /* Fija la barra en la página */
            left: 0;
            top: 0;
            width: 50px; /* Ancho de la sangría */
            height: 100%;
            background-color: #e6f7ee; /* Un verde muy tenue */
            padding-top: 10px; /* Para alinear con el margen superior del body */
            box-sizing: border-box;
            z-index: 1;
        }

        .sidebar-logo {
            width: 50px; /* Ajusta al ancho del sidebar */
            display: block;
            margin: 0 auto; /* Centrar el logo */
        }

        .sidebar-text {
            /* Rotación de 90 grados (sentido horario) */
            transform: rotate(-90deg);
            transform-origin:  bottom left;

            /* Posicionamiento del texto rotado */
            position: absolute;
            left: 35px; /* Ajusta la posición horizontal */
            top: 350px; /* Ajusta la posición vertical (más abajo del logo) */

            /* Estilo del texto */
            white-space: nowrap; /* Evita que el texto se parta */
            font-size: 22px;
            font-weight: bold;
            color: #2b2e2d; /* Verde oscuro para contraste */
            letter-spacing: 2px;
        }

        /* Reducción general de tamaño y espaciado */
        body {
            font-family: Arial, sans-serif;
            margin: 15px 25px 80px 60px; /* Margen inferior para el pie de página fijo */
            padding: 0;
            font-size: 7.5px; /* Fuente ligeramente más pequeña */
        }

        /* Estilos para tablas */
        .main-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border: 1px solid #000;
            border-radius: 10px; /* Reducción del radio */
            overflow: hidden;
            margin-bottom: 5px; /* Reducir espacio entre tablas */
        }

        .main-table td {
            border: 1px solid #000;
            padding: 2px 4px; /* Reducción de padding en celdas */
            line-height: 1.2; /* Ajustar el interlineado */
        }

        /* Estilos de encabezado */
        .header {
            background-color: #009959;
            color: white;
            font-weight: bold;
            padding: 3px;
            text-align: center;
        }

        .header-gray {
            background-color: #bdc0be;
            color: white;
            font-weight: bold;
            padding: 3px;
            text-align: center;
        }

        /* Estilos para campos de texto (inputs) */
        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="date"] {
            width: 98%;
            padding: 0px;
            margin: 0;
            border: none;
            background-color: transparent;
            font-size: 8px !important; /* Ajuste para inputs */
            font-weight: bold !important;
            box-sizing: border-box;
        }

        .input-inline-date input[type="number"] {
            width: 30px !important;
            text-align: center;
        }

        input[type="checkbox"] {
            margin: 0 3px;
        }

        /* Título del documento */
        .title {
            font-size: 14px; /* Reducir tamaño de título */
            font-weight: bold;
            text-align: right;
            line-height: 1.2;
            margin-bottom: 5px;
        }

        /* Descripción */
        .description {
            font-size: 7px;
            padding: 5px;
            margin-top: 5px;
        }

        /* Pie de página fijo */
        .footer {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70px; /* Altura del pie de página */
            text-align: center;
            padding: 5px 25px 5px 25px;
            margin: 0;
            font-size: 7px;
            background-color: white; /* Para asegurar que no se solape el contenido */
            border-top: 1px solid #ccc;
        }

        .footer-title {
            color: #009959;
            font-weight: bold;
            margin-bottom: 2px;
        }

        .footer-contact {
            margin-top: 2px;
        }

        /* Estilos para la tabla de análisis de crédito */
        .second-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            margin-top: 5px;
        }

        .second-table td {
            border: none;
            padding: 4px 5px;
            border-bottom: 1px dashed #ccc; /* Separación sutil entre filas */
        }

        .second-table input[type="text"],
        .second-table textarea {
            border-bottom: 1px solid #000;
            padding: 0 2px;
            font-size: 7.5px !important;
        }

        .second-table textarea {
            box-sizing: border-box;
            resize: none;
            overflow: hidden;
            height: 30px;
        }

        /* Estilos para textos de contratos/autorización */
        .contract-text {
            font-size: 7px;
            text-align: justify;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <div class="sidebar-bleed">
        <img src="{{ public_path('images/logo_bg.png') }}" alt="Logo" class="sidebar-logo">
        <div class="sidebar-text">
            SOLICITUD DE CRÉDITO
        </div>
    </div>

    <div class="footer">
        <div class="footer-title">
            FONDO NACIONAL DE EMPLEADOS, TRABAJADORES Y PENSIONADOS DEL SECTOR POSTAL<br>
            DE LAS COMUNICACIONES, ENTIDADES AFINES Y COMPLEMENTARIAS - FONDEP
        </div>
        <div>Calle 24-D Bis No. 73-C - 48, Tels: (601) 548 1317 - (601) 295 0229 WAPP: 322 423 04 02 Bogotá,
            D.C. - Colombia</div>
        <div class="footer-contact">comunicaciones@fondep.com.co • www.fondep.com.co</div>
    </div>

    <div class="page-container">
        <div class="main-content">
            <table class="main-table">
                <tr>
                    <td colspan="4" class="header">CRÉDITO SOLICITADO</td>
                </tr>
                <tr>
                    <td width="25%">MONTO $<input type="number" value="{{ $formatNumber($credito['vlr_solicitud']) }}"></td>
                    <td width="25%">EN LETRAS<input type="text" value=""></td>
                    <td colspan="2" width="50%"></td>
                </tr>
                <tr>
                    <td>PLAZO EN MESES(CUOTAS)<input type="number" value="{{ $credito['nro_cuotas_max'] ?? '' }}"></td>
                    <td>VALOR CUOTA MENSUAL $<input type="number" value="{{ $formatNumber($credito['vlr_planes']) }}"></td>
                    <td colspan="2" style="font-size: 5px; vertical-align: middle; padding-top: 5px;">
                        LÍNEA CRÉDITO:
                        VIVIENDA <input type="checkbox">
                        LIBRE INVERSIÓN <input type="checkbox">
                        VEHÍCULO <input type="checkbox">
                        OTRO <input type="checkbox"> CUÁL <input type="text" style="width: 80px;">
                    </td>
                </tr>

                <tr>
                    <td width="25%">
                        FAVOR ABONAR EL DESEMBOLSO DE ESTE CRÉDITO A MI CUENTA<br>
                        BANCO <input type="text" style="width: 150px;">
                    </td>
                    <td width="25%" style="font-size: 7px; vertical-align: middle; padding-top: 5px;">
                        AHORROS <input type="checkbox">
                        CORRIENTE <input type="checkbox"><br>
                        No. <input type="text" style="width: 170px;">
                    </td>
                    <td colspan="2" width="15%" style="vertical-align: top;">
                        O GIRAR CHEQUE A
                    </td>

                </tr>

                <tr>
                    <td colspan="2">
                        OFICINA RECEPTORA<br>
                        <input type="text" style="width: 110%; text-align: center;" value="Fondep Principal">
                    </td>
                    <td>
                        CIUDAD<br>
                        <input type="text" style="width: 110%; text-align: center;" value="Bogotá">
                    </td>
                    <td>
                        FECHA<br>
                        <div class="input-inline-date" style="display: flex; gap: width: 110%; text-align: center;">
                            <input type="number" placeholder="DD" value="{{ now()->format('d') }}" min="1" max="31">
                            <input type="number" placeholder="MM" value="{{ now()->format('m') }}" min="1" max="12">
                            <input type="number" placeholder="AAAA" value="{{ now()->format('Y') }}" style="width: 45px !important;">
                        </div>
                    </td>
                </tr>
            </table>

            <table class="main-table">
                <tr>
                    <td colspan="4" class="header">INFORMACIÓN PERSONAL - DEUDOR</td>
                </tr>
                <tr>
                    <td width="25%">PRIMER APELLIDO<br><input type="text" value="{{ $tercero['primer_apellido'] ?? '' }}"></td>
                    <td width="25%">SEGUNDO APELLIDO<br><input type="text" value="{{ $tercero['segundo_apellido'] ?? '' }}"></td>
                    <td colspan="2">NOMBRES<br><input type="text" value="{{ $tercero['nombres'] ?? '' }}"></td>
                </tr>
                <tr>
                    <td colspan="2">Empresa<br><input type="text" value="{{ $asociado['empresa'] ?? '' }}"></td>
                    <td>Ocupacion<br><input type="text" value="{{ $asociado['actividad_economica_id'] ?? '' }}"></td>
                    <td>Fecha Ingreso<br><input type="text" value="{{ $asociado['fecha_ingreso'] ?? '' }}"></td>
                </tr>
                <tr>
                    <td>
                        FECHA DE NACIMIENTO<br>
                        <input type="text" value="{{ $asociado['fecha_nacimiento'] ?? '' }}">
                    </td>
                    <td>CÉDULA<br><input type="text" value="{{ $tercero['tercero_id'] ?? ''}}"></td>
                    <td>EXPEDIDA EN<br><input type="text"></td>
                    <td style="font-size: 5px; vertical-align: middle; padding-top: 5px;">
                        EMPLEADO ACTIVO <input type="checkbox">
                        PENSIONADO <input type="checkbox">
                    </td>
                </tr>
                <tr>
                    <td>DIRECCIÓN RESIDENCIA<br><input type="text" value="{{ $direccionTercero }}">
                    </td>
                    <td>TELÉFONO<br><input type="text" value="{{ $tercero['telefono'] ?? '' }}"></td>
                    <td>CELULAR<br><input type="text" value="{{ $tercero['celular'] ?? '' }}"></td>
                    <td>CIUDAD<br><input type="text"></td>
                </tr>
                <tr>
                    <td colspan="2">DIRECCIÓN LABORAL<br><input type="text" value="{{ $asociado['direccion_empresa'] ?? '' }}"></td>
                    <td>TELÉFONO<br><input type="text" value="{{ $asociado['telefono_empresa'] ?? '' }}"></td>
                    <td>CIUDAD<br><input type="text"></td>
                </tr>
                <tr>
                    <td colspan="2">CORREO ELECTRÓNICO (E-MAIL)<br><input type="email" value="{{ $tercero['email'] ?? '' }}"></td>
                    <td colspan="2" style="font-size: 6px; vertical-align: middle; padding-top: 5px;">
                        ENVÍO CORRESPONDENCIA:
                        OFICINA <input type="checkbox">
                        RESIDENCIA <input type="checkbox">
                    </td>
                </tr>
            </table>

            <table class="main-table">
                <tr>
                    <td colspan="4" class="header">INFORMACIÓN FINANCIERA - DEUDOR</td>
                </tr>
                <tr>
                    <td colspan="4" style="text-align: center;">
                        ACTIVOS: $ <input type="text" value="{{ $formatNumber($finanzas['total_activos'] ?? 0) }}" style="width: 15%; text-align: center;">
                        &nbsp;&nbsp; PASIVOS: $ <input type="text" value="{{ $formatNumber($finanzas['total_pasivos'] ?? 0) }}" style="width: 15%; text-align: center;">
                        &nbsp;&nbsp; PATRIMONIO: $ <input type="text" value="{{ $formatNumber($finanzas['total_patrimonio'] ?? 0) }}" style="width: 15%; text-align: center;">
                    </td>
                </tr>
                <tr class="sub-header">
                    <td colspan="2" class="header">INGRESOS MENSUALES</td>
                    <td colspan="2" class="header">GASTOS MENSUALES</td>
                </tr>
                <tr>
                    <td>SALARIO</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['salario'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                    <td>GASTOS DE SOSTENIMIENTO</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['gastos_sostenimiento'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                </tr>
                <tr>
                    <td>SERVICIOS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['servicios'] ?? 0) }}" style="width: 15%; text-align: left;""></td>
                    <td>GASTOS FINANCIEROS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['gastos_financieros'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                </tr>
                <tr>
                    <td>OTROS INGRESOS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['otros_ingresos'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                    <td>ARRIENDOS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['arriendos'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>OTROS GASTOS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['otros_gastos'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>GASTOS PERSONALES</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['gastos_personales'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                </tr>
                <tr style="font-weight: bold; background: #f5f5f5;">
                    <td>TOTAL INGRESOS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['total_ingresos'] ?? 0) }}" style="width: 15%; text-align: left;"></td>
                    <td>TOTAL GASTOS</td>
                    <td>$ <input type="text" value="{{ $formatNumber($finanzas['total_gastos'] ?? 0) }}" style="width: 15%;  text-align: left;"></td>
                </tr>
            </table>

            <div class="main-content">
                <table class="main-table">
                    <tr>
                        <td colspan="4" class="header">INFORMACIÓN PERSONAL - DEUDOR SOLIDARIO 1</td>
                    </tr>
                    <tr>
                        <td width="25%">PRIMER APELLIDO<br><input type="text" value=""></td>
                        <td width="25%">SEGUNDO APELLIDO<br><input type="text" value=""></td>
                        <td colspan="2">NOMBRES<br><input type="text" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">Empresa<br><input type="text" value=""></td>
                        <td>Ocupacion<br><input type="text" value=""></td>
                        <td>Fecha Ingreso<br><input type="text" value=""></td>
                    </tr>
                    <tr>
                        <td>
                            FECHA DE NACIMIENTO<br>
                            <input type="text" value="">
                        </td>
                        <td>CÉDULA<br><input type="text" value=""></td>
                        <td>EXPEDIDA EN<br><input type="text"></td>
                        <td style="font-size: 5px; vertical-align: middle; padding-top: 5px;">
                            EMPLEADO ACTIVO <input type="checkbox">
                            PENSIONADO <input type="checkbox">
                        </td>
                    </tr>
                    <tr>
                        <td>DIRECCIÓN RESIDENCIA<br><input type="text" value="">
                        </td>
                        <td>TELÉFONO<br><input type="text" value=""></td>
                        <td>CELULAR<br><input type="text" value=""></td>
                        <td>CIUDAD<br><input type="text"></td>
                    </tr>
                    <tr>
                        <td colspan="2">DIRECCIÓN LABORAL<br><input type="text" value=""></td>
                        <td>TELÉFONO<br><input type="text" value=""></td>
                        <td>CIUDAD<br><input type="text" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">CORREO ELECTRÓNICO (E-MAIL)<br><input type="email" value=""></td>
                        <td colspan="2" style="font-size: 6px; vertical-align: middle; padding-top: 5px;">
                            ENVÍO CORRESPONDENCIA:
                            OFICINA <input type="checkbox">
                            RESIDENCIA <input type="checkbox">
                        </td>
                    </tr>
                </table>

                <table class="main-table">
                    <tr>
                        <td colspan="4" class="header">INFORMACIÓN FINANCIERA - DEUDOR SOLIDARIO 1</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;">
                            ACTIVOS: $ <input type="text" value="" style="width: 15%; text-align: center;">
                            &nbsp;&nbsp; PASIVOS: $ <input type="text" value="" style="width: 15%; text-align: center;">
                            &nbsp;&nbsp; PATRIMONIO: $ <input type="text" value="" style="width: 15%; text-align: center;">
                        </td>
                    </tr>
                    <tr class="sub-header">
                        <td colspan="2" class="header">INGRESOS MENSUALES</td>
                        <td colspan="2" class="header">GASTOS MENSUALES</td>
                    </tr>
                    <tr>
                        <td>SALARIO</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>GASTOS DE SOSTENIMIENTO</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td>SERVICIOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>GASTOS FINANCIEROS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td>OTROS INGRESOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>ARRIENDOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>OTROS GASTOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>GASTOS PERSONALES</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr style="font-weight: bold; background: #f5f5f5;">
                        <td>TOTAL INGRESOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>TOTAL GASTOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                </table>

                <table class="main-table">
                    <tr>
                        <td colspan="4" class="header">INFORMACIÓN PERSONAL - DEUDOR SOLIDARIO 2</td>
                    </tr>
                    <tr>
                        <td width="25%">PRIMER APELLIDO<br><input type="text" value=""></td>
                        <td width="25%">SEGUNDO APELLIDO<br><input type="text" value=""></td>
                        <td colspan="2">NOMBRES<br><input type="text" value=""></td>
                    </tr>
                    <tr>
                        <td colspan="2">Empresa<br><input type="text" value=""></td>
                        <td>Ocupacion<br><input type="text" value=""></td>
                        <td>Fecha Ingreso<br><input type="text" value=""></td>
                    </tr>
                    <tr>
                        <td>
                            FECHA DE NACIMIENTO<br>
                            <input type="text" value="">
                        </td>
                        <td>CÉDULA<br><input type="text" value=""></td>
                        <td>EXPEDIDA EN<br><input type="text"></td>
                        <td style="font-size: 5px; vertical-align: middle; padding-top: 5px;">
                            EMPLEADO ACTIVO <input type="checkbox">
                            PENSIONADO <input type="checkbox">
                        </td>
                    </tr>
                    <tr>
                        <td>DIRECCIÓN RESIDENCIA<br><input type="text" value="">
                        </td>
                        <td>TELÉFONO<br><input type="text" value=""></td>
                        <td>CELULAR<br><input type="text" value=""></td>
                        <td>CIUDAD<br><input type="text"></td>
                    </tr>
                    <tr>
                        <td colspan="2">DIRECCIÓN LABORAL<br><input type="text" value=""></td>
                        <td>TELÉFONO<br><input type="text" value=""></td>
                        <td>CIUDAD<br><input type="text"></td>
                    </tr>
                    <tr>
                        <td colspan="2">CORREO ELECTRÓNICO (E-MAIL)<br><input type="email" value=""></td>
                        <td colspan="2" style="font-size: 6px; vertical-align: middle; padding-top: 5px;">
                            ENVÍO CORRESPONDENCIA:
                            OFICINA <input type="checkbox">
                            RESIDENCIA <input type="checkbox">
                        </td>
                    </tr>
                </table>

                <table class="main-table">
                    <tr>
                        <td colspan="4" class="header">INFORMACIÓN FINANCIERA - DEUDOR SOLIDARIO 2</td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center;">
                            ACTIVOS: $ <input type="text" value="" style="width: 15%; text-align: center;">
                            &nbsp;&nbsp; PASIVOS: $ <input type="text" value="" style="width: 15%; text-align: center;">
                            &nbsp;&nbsp; PATRIMONIO: $ <input type="text" value="" style="width: 15%; text-align: center;">
                        </td>
                    </tr>
                    <tr class="sub-header">
                        <td colspan="2" class="header">INGRESOS MENSUALES</td>
                        <td colspan="2" class="header">GASTOS MENSUALES</td>
                    </tr>
                    <tr>
                        <td>SALARIO</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>GASTOS DE SOSTENIMIENTO</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td>SERVICIOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>GASTOS FINANCIEROS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td>OTROS INGRESOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>ARRIENDOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>OTROS GASTOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>GASTOS PERSONALES</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                    <tr style="font-weight: bold; background: #f5f5f5;">
                        <td>TOTAL INGRESOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                        <td>TOTAL GASTOS</td>
                        <td>$ <input type="text" value="" style="text-align: right;"></td>
                    </tr>
                </table>



                <table class="main-table">
                    <tr>
                        <td colspan="3" style="padding: 155px 10px 0 10px;">
                            <table style="width: 100%; border-collapse: collapse; table-layout: fixed; margin: 0;">
                                <tr>
                                    <td style="width: 33.3%; text-align: center; padding: 5px 0 0 0; border-top: 1px solid #000; font-size: 7px; line-height: 1.2;">
                                        Firma, C.C. y huella del solicitante
                                    </td>
                                    <td style="width: 33.3%; text-align: center; padding: 5px 0 0 0; border-top: 1px solid #000; font-size: 7px; line-height: 1.2;">
                                        Firma, C.C. y huella deudor solidario 1
                                    </td>
                                    <td style="width: 33.3%; text-align: center; padding: 5px 0 0 0; border-top: 1px solid #000; font-size: 7px; line-height: 1.2;">
                                        Firma, C.C. y huella deudor solidario 2
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 10px 10px; border: none;">
                            <p class="contract-text">
                                El(los) abajo firmante(s), de manera expresa, declara(mos) adeudar solidariamente a FONDEP
                                la suma de dinero solicitada en mutuo con intereses, mediante la presente solicitud, una vez
                                la misma sea aprobada y me(nos) obligo(amos) a pagar en la ciudad de Bogotá, la suma de
                                dinero a que hace referencia esta solicitud, a la tasa de interés que corresponda a la línea
                                de crédito aprobada, en la forma y plazo indicados en este documento. Acepto(amos)
                                expresamente que FONDEP, haga exigible la totalidad de la obligación referida, de
                                presentarse mora en el pago de una o varias de las cuotas establecidas dentro del plazo y en
                                general de configurarse uno cualquiera de los eventos establecidos como extintivos del
                                plazo, tanto en el pagaré como en la carta de instrucciones para llenar el pagaré en blanco
                                que diligencio(amos) para garantizar el pago de la obligación aquí contenida.
                            </p>
                            <p class="contract-text">
                                En caso de mora, autorizo expresa e irrevocablemente a FONDEP, abonar del saldo de mis
                                depósitos de ahorro voluntario, o cualquier suma a mi favor, a las cuotas de crédito
                                solicitado. De igual manera, en caso de que la mora supere los noventa (90) días, autorizo
                                para abonar del saldo de mi ahorro voluntario, el valor de los honorarios y gastos de
                                cobranza que se originen, a FONDEP o a la entidad que ésta contrate para tal fin. Lo
                                anterior sin que requiera previo aviso al deudor.
                            </p>
                            <p class="contract-text">
                                Así mismo, manifiesto(amos) que acepto(amos) el cargo del valor del seguro de vida a
                                deudores y en caso de generarse algún devuelto, me(nos) comprometo(emos) a cancelarlo dentro
                                de los cinco (5) primeros días de cada mes.
                            </p>
                            <p class="contract-text">
                                Declaro(amos) que conozco(cemos) el reglamento de crédito de la entidad y el plan de pagos
                                correspondiente al crédito solicitado.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" class="sub-header" style="text-align: center; padding: 5px 10px; margin-top: 10px; border: none;">
                            AUTORIZACIÓN CONSULTA Y REPORTE A CENTRALES DE RIESGO
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 5px 10px; border: none;">
                            <p class="contract-text">
                                El(los) abajo firmante(s) autorizo(amos) al Fondo Nacional de Empleados, trabajadores y
                                Pensionados del sector postal de las comunicaciones, entidades afines y complementarias
                                FONDEP o a quien represente sus derechos u ostente en el futuro la calidad de acreedor, a
                                reportar, procesar, solicitar y divulgar a cualquier entidad que maneje o administre bases
                                de datos que contengan el comportamiento crediticio o comercial de personas, toda
                                información.
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 155px 10px 0 10px;">
                            <table style="width: 100%; border-collapse: collapse; table-layout: fixed; margin: 0;">
                                <tr>
                                    <td style="width: 33.3%; text-align: center; padding: 5px 0 0 0; border-top: 1px solid #000; font-size: 7px; line-height: 1.2;">
                                        Firma, C.C. y huella del solicitante
                                    </td>
                                    <td style="width: 33.3%; text-align: center; padding: 5px 0 0 0; border-top: 1px solid #000; font-size: 7px; line-height: 1.2;">
                                        Firma, C.C. y huella deudor solidario 1
                                    </td>
                                    <td style="width: 33.3%; text-align: center; padding: 5px 0 0 0; border-top: 1px solid #000; font-size: 7px; line-height: 1.2;">
                                        Firma, C.C. y huella deudor solidario 2
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" style="padding: 50px 10px; border: none; border-bottom: 1px solid #000;">
                            <div style="font-weight: bold; margin-bottom: 5px;">ANEXAR LA SIGUIENTE DOCUMENTACIÓN</div>
                            <table width="100%" style="border: none; border-spacing: 0;">
                                <tr valign="top">
                                    <td width="50%">
                                        <ol style="margin: 0; padding-left: 20px;">
                                            <li>Tres (3) últimos comprobantes de pago</li>
                                            <li>Fotocopia de la cédula ampliada al 150%</li>
                                            <li>Documentos que sirvan de soporte para demostrar ingresos adicionales</li>
                                        </ol>
                                    </td>
                                    <td width="50%">
                                        <ol start="4" style="margin: 0; padding-left: 20px;">
                                            <li>Pagaré en blanco firmado y con impresión de la huella dactilar</li>
                                            <li>Carta de instrucciones diligenciada y con impresión de la huella dactilar</li>
                                        </ol>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>

                <table class="second-table" style="margin-top: 50px; width: 100%; border-collapse: collapse; font-size: 12px;">
                    <tr>
                        <td colspan="4" style="text-align: center; font-weight: bold; border-bottom: 1px solid #000;">
                            ANÁLISIS DE LA SOLICITUD DE CRÉDITO POR PARTE DE “FONDEP”
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 25%;">Saldo de aportes sociales:</td>
                        <td style="width: 25%;">$ <input type="text" style="width: 90%; text-align: right;"></td>
                        <td style="width: 15%;">Estado:</td>
                        <td><input type="text" style="width: 90%;"></td>
                    </tr>
                    <tr>
                        <td>Saldo de crédito a largo plazo:</td>
                        <td>$ <input type="text" style="width: 90%; text-align: right;"></td>
                        <td>Estado:</td>
                        <td><input type="text" style="width: 90%;"></td>
                    </tr>
                    <tr>
                        <td>Saldo de crédito a corto plazo:</td>
                        <td>$ <input type="text" style="width: 90%; text-align: right;"></td>
                        <td>Estado:</td>
                        <td><input type="text" style="width: 90%;"></td>
                    </tr>
                    <tr>
                        <td>Saldo de crédito de consumo:</td>
                        <td>$ <input type="text" style="width: 90%; text-align: right;"></td>
                        <td>Estado:</td>
                        <td><input type="text" style="width: 90%;"></td>
                    </tr>
                    <tr>
                        <td colspan="2">Estado de cuenta suministrado y verificado por:
                            <input type="text" style="width: 65%;">
                        </td>
                        <td colspan="2">Fecha: <input type="text" style="width: 70%;"></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Resultado y observaciones a la verificación de la información telefónica realizada en la fecha de:
                            <input type="text" style="width: 98%;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Verificación efectuada por: <input type="text" style="width: 75%;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4" style="text-align: center; font-weight: bold; border-top: 1px dashed #000; padding-top: 45px;">
                            RESULTADO FINAL DE LA SOLICITUD DE CRÉDITO PRESENTADA
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Solicitud de crédito aprobada por:
                            Junta Directiva <input type="checkbox">
                            Comité de crédito <input type="checkbox">
                            Gerencia <input type="checkbox">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Según consta en el Acta número: <input type="text" style="width: 80px;">
                            Fecha: <input type="text" style="width: 100px;">
                            Solicitud negada <input type="checkbox">
                            Aplazada <input type="checkbox">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Solicitud aprobada <input type="checkbox">
                            Aprobada por valor de: $<input type="text" style="width: 120px; text-align: right;">
                            Plazo aprobado: <input type="text" style="width: 60px;"> Meses
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Valor cuota mensual: $<input type="text" style="width: 80%; text-align: right;"></td>
                        <td colspan="2">Fecha de pago de la primera cuota: <input type="text" style="width: 70%;"></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Observaciones formuladas por el órgano directivo sobre la aprobación final del crédito solicitado:
                            <textarea style="width: 98%; height: 40px;"></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Crédito desembolsado el día: <input type="text" style="width: 70%;"></td>
                        <td colspan="2">Número de comprobante de egreso: <input type="text" style="width: 60%;"></td>
                    </tr>
                    <tr>
                        <td colspan="4">
                            Número de cheque <input type="text" style="width: 100px;">
                            Consignado en banco: <input type="text" style="width: 180px;">
                            No. de cuenta: <input type="text" style="width: 150px;">
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">Aprobado por: <input type="text" style="width: 80%;"></td>
                        <td colspan="2">Firma Aprobado: <input type="text" style="width: 80%;"></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
