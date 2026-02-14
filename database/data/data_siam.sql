/* Clasificacion de creditos */

insert into clasificacion_creditos (CLASIFICACION, DESCRIPCION, NRO_SALARIOS_MIN, NRO_SALARIOS_MAX, PUC_CAUSA_CXC, PUC_CAUSA_INGRESOS, PUC_CAUSA_GASTOS, PUC_CAUSA_CTAS_ORDEN, PORC_CAUSACION, PUC_APROBACION, PUC_CONTRA_PARTIDA, PUC_PROVISION, PUC_PROV_INT, PUC_PROV_INT_REV, PUC_PROV_REV)
values ('1', 'CONSUMO', 1, 9999, '9', '9', '9', '9', 0, '980505', '911505', '51151501', '1', '51151801', '51151501');

insert into clasificacion_creditos (CLASIFICACION, DESCRIPCION, NRO_SALARIOS_MIN, NRO_SALARIOS_MAX, PUC_CAUSA_CXC, PUC_CAUSA_INGRESOS, PUC_CAUSA_GASTOS, PUC_CAUSA_CTAS_ORDEN, PORC_CAUSACION, PUC_APROBACION, PUC_CONTRA_PARTIDA, PUC_PROVISION, PUC_PROV_INT, PUC_PROV_INT_REV, PUC_PROV_REV)
values ('2', 'COMERCIAL', 1, 999, '9', '9', '9', '9', 0, '911505', '980505', null, '1', '511530', null);

insert into clasificacion_creditos (CLASIFICACION, DESCRIPCION, NRO_SALARIOS_MIN, NRO_SALARIOS_MAX, PUC_CAUSA_CXC, PUC_CAUSA_INGRESOS, PUC_CAUSA_GASTOS, PUC_CAUSA_CTAS_ORDEN, PORC_CAUSACION, PUC_APROBACION, PUC_CONTRA_PARTIDA, PUC_PROVISION, PUC_PROV_INT, PUC_PROV_INT_REV, PUC_PROV_REV)
values ('3', 'VIVIENDA', 1, 999, '9', '9', '9', '9', 0, '911505', '980505', null, '1', '511530', null);

insert into clasificacion_creditos (CLASIFICACION, DESCRIPCION, NRO_SALARIOS_MIN, NRO_SALARIOS_MAX, PUC_CAUSA_CXC, PUC_CAUSA_INGRESOS, PUC_CAUSA_GASTOS, PUC_CAUSA_CTAS_ORDEN, PORC_CAUSACION, PUC_APROBACION, PUC_CONTRA_PARTIDA, PUC_PROVISION, PUC_PROV_INT, PUC_PROV_INT_REV, PUC_PROV_REV)
values ('4', 'MICROCREDITO', 1, 999, '9', '9', '9', '9', 9, '911505', '980505', null, '1', '511530', null);

/* Tipo inversiones */
insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (25, 'Préstamos Vehículo');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (27, 'Préstamos Electrodomésticos');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (30, 'Préstamos con recursos externos');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (35, 'Préstamos Educativos');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (40, 'Préstamos Calamidad');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (43, 'Factoring');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (72, 'Fianzas y avales cubiertas');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (75, 'Otros préstamos');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (100, 'Préstamos para vivienda');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (1, 'LIBRE INVERSION');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (5, 'SALUD');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (8, 'COMPRA VEHICULO');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (12, 'ROTATORIO');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (13, 'IMPUESTOS');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (16, 'EXTRAORDINARIO');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (17, 'VIVIENDA');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (19, 'LINEA 2000');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (20, 'EDUCACION');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (21, 'ESPECIAL');

insert into tipo_inversiones (TIPO_INVERSION, DESCRIPCION)
values (15, 'Préstamos Ordinarios');


-- calcucalcular_amortizacion
CREATE OR REPLACE FUNCTION calcular_amortizacion(
    principal NUMERIC,
    tasa_anual NUMERIC,
    plazo_meses INT
) RETURNS TABLE (
    periodo INT,
    pago NUMERIC,
    interes NUMERIC,
    amortizacion_capital NUMERIC,
    saldo NUMERIC
) AS $$
DECLARE
    tasa_mensual NUMERIC;
    pago_mensual NUMERIC;
    saldo_actual NUMERIC := principal;
    interes_actual NUMERIC;
    amortizacion_capital_actual NUMERIC;
    i INT;
BEGIN
    -- Calcular tasa mensual
    tasa_mensual := tasa_anual / 12 / 100;

    -- Calcular pago mensual
    IF tasa_mensual > 0 THEN
        pago_mensual := principal * tasa_mensual * POWER((1 + tasa_mensual), plazo_meses) /
                        (POWER((1 + tasa_mensual), plazo_meses) - 1);
    ELSE
        pago_mensual := principal / plazo_meses;
    END IF;

    -- Generar la tabla de amortización
    FOR i IN 1..plazo_meses LOOP
        interes_actual := saldo_actual * tasa_mensual;
        amortizacion_capital_actual := pago_mensual - interes_actual;
        saldo_actual := saldo_actual - amortizacion_capital_actual;

        -- Asignar valores a las columnas de salida
        periodo := i;
        pago := ROUND(pago_mensual, 2);
        interes := ROUND(interes_actual, 2);
        amortizacion_capital := ROUND(amortizacion_capital_actual, 2);
        saldo := ROUND(saldo_actual, 2);

        RETURN NEXT; -- Devuelve la fila actual
    END LOOP;
END;
$$ LANGUAGE plpgsql;

-- generar_cuotas_y_detalles
CREATE OR REPLACE PROCEDURE generar_cuotas_y_detalles(
    documento BIGINT
) AS $$
DECLARE
    cuota RECORD;
    fecha_vencimiento DATE := NOW();
    concepto RECORD;
    forma_descuento TEXT;
    vlr_solicitud NUMERIC;
    tasa_float NUMERIC;
    nro_cuotas_max INT;
    cuotas_canceladas INT := 0;
    nro_cuota_inicio INT := 1;
BEGIN
    -- Eliminar registros existentes en cuotas_encabezados y cuotas_detalles para el nro_docto en estado 'A'
    DELETE FROM cuotas_detalles
    WHERE nro_docto = documento AND estado = 'A';

    DELETE FROM cuotas_encabezados
    WHERE nro_docto = documento AND estado = 'A';

    -- Obtener datos de cartera_encabezados
    SELECT ce.vlr_saldo_actual, ce.interes_cte, ce.nro_cuotas, ce.forma_descuento
    INTO vlr_solicitud, tasa_float, nro_cuotas_max, forma_descuento
    FROM cartera_encabezados ce
    WHERE ce.nro_docto = documento AND tdocto = 'PAG' AND estado = 'A';

    -- Verificar si se encontró el registro en cartera_encabezados
    IF NOT FOUND THEN
        RAISE EXCEPTION 'No se encontró el documento % en cartera_encabezados', documento;
    END IF;

    -- Obtener la cantidad de cuotas ya canceladas
    SELECT COALESCE(COUNT(*), 0)
    INTO cuotas_canceladas
    FROM cuotas_encabezados
    WHERE nro_docto = documento AND estado = 'C';

    -- Ajustar el número máximo de cuotas
    nro_cuotas_max := GREATEST(nro_cuotas_max - cuotas_canceladas, 0);

    -- Obtener el número de la última cuota cancelada y establecer la siguiente como inicio
    SELECT COALESCE(MAX(nro_cuota), 0) + 1
    INTO nro_cuota_inicio
    FROM cuotas_encabezados
    WHERE nro_docto = documento AND estado = 'C';

    -- Validar que todavía haya cuotas por generar
    IF nro_cuotas_max = 0 THEN
        RAISE EXCEPTION 'No hay cuotas pendientes por generar para el documento %', documento;
    END IF;

    -- Calcular las cuotas de amortización e insertar en la tabla directamente
    FOR cuota IN SELECT * FROM calcular_amortizacion(vlr_solicitud, tasa_float, nro_cuotas_max) LOOP
        -- Incrementar un mes a partir de la segunda iteración
        IF cuota.periodo > 1 THEN
            fecha_vencimiento := fecha_vencimiento + INTERVAL '1 month';
        END IF;

        -- Insertar la cuota en la tabla cuotas_encabezados
        INSERT INTO cuotas_encabezados (
            tdocto, nro_docto, nro_cuota, consecutivo, estado, iden_cuota,
            interes_cte, interes_mora, fecha_vencimiento, fecha_pago_total, dias_mora,
            vlr_cuota, saldo_capital, vlr_abono_rec, vlr_abono_ncr, vlr_abono_dpa,
            vlr_descuento, forma_descuento, vlr_cuentas_orden, vlr_causado
        ) VALUES (
            'PAG',
            documento,
            nro_cuota_inicio + cuota.periodo - 1, -- Ajuste de la numeración de cuotas
            1,
            'A',
            'N',
            tasa_float,
            0.00,
            fecha_vencimiento,
            NULL,
            0,
            cuota.pago,
            cuota.saldo,
            0.00,
            0.00,
            0.00,
            0.00,
            forma_descuento,
            0.00,
            cuota.amortizacion_capital
        );
    END LOOP;

    -- Insertar los detalles de cada cuota generada
    FOR concepto IN SELECT * FROM cartera_composicion_conceptos WHERE numero_documento = documento AND tipo_documento = 'PAG' LOOP
        FOR cuota IN SELECT nro_cuota, vlr_causado FROM cuotas_encabezados WHERE nro_docto = documento AND estado = 'A' LOOP
            INSERT INTO cuotas_detalles (
                tdocto, nro_docto, nro_cuota, consecutivo, estado, vlr_detalle, con_descuento
            ) VALUES (
                'PAG',
                documento,
                cuota.nro_cuota,
                1,
                'A',
                CASE
                    WHEN concepto.concepto_descuento = 1 THEN cuota.vlr_causado
                    WHEN concepto.concepto_descuento = 2 THEN (SELECT interes_cte FROM cuotas_encabezados WHERE nro_docto = documento AND nro_cuota = cuota.nro_cuota)
                    ELSE 0.00
                END,
                concepto.concepto_descuento
            );
        END LOOP;
    END LOOP;

	UPDATE cuotas_encabezados
	SET vlr_causado = 0.00
	WHERE nro_docto = documento AND estado = 'A' AND tdocto = 'PAG';
END;
$$ LANGUAGE plpgsql;


-- FUNCTION: public.generar_comprobante_v2(integer, integer, text, numeric, numeric, numeric)

-- DROP FUNCTION IF EXISTS public.generar_comprobante_v2(integer, integer, text, numeric, numeric, numeric);

CREATE OR REPLACE FUNCTION public.generar_comprobante_v2(
	p_cliente_id integer,
	p_tipo_documento_id integer,
	p_tipo_pago text,
	p_efectivo numeric,
	p_cheque numeric,
	p_valor_abonar numeric)
    RETURNS void
    LANGUAGE 'plpgsql'
    COST 100
    VOLATILE PARALLEL UNSAFE
AS $BODY$
DECLARE
    v_cliente_id INT;
    v_sigla_documento TEXT;
    v_now DATE;
    v_consecutivo INT;
    v_comprobante_id INT;
    v_cuenta_capital BIGINT;
    v_linea_credito RECORD;
    v_cuenta_contable INT;
    v_campo_abono TEXT;
	v_usuario TEXT;
    v_sumatoria NUMERIC;
    v_sumatoria_total NUMERIC;
    v_cartera_encabezado RECORD;
    v_cuota_encabezado RECORD;
    v_cuota_detalle RECORD;
    v_liquidacion RECORD;
    v_obligacion RECORD;
    v_otro_concepto RECORD;
	v_numerador INT;
BEGIN
    -- Iniciar transacción
    BEGIN
        -- Obtener datos del cliente y tipo de documento
        SELECT tercero_id INTO v_cliente_id FROM terceros WHERE tercero_id = p_cliente_id::varchar;
        SELECT sigla, numerador INTO v_sigla_documento, v_numerador FROM tipo_documento_contables WHERE id = p_tipo_documento_id;
		SELECT id INTO v_cuenta_capital FROM pucs WHERE puc = p_tipo_pago;
        v_now := CURRENT_DATE;
        v_usuario := 'admin';

        -- Recorrer las carteras encabezados del cliente
        FOR v_cartera_encabezado IN
            SELECT * FROM cartera_encabezados WHERE cliente = v_cliente_id AND estado = 'A' AND tdocto = 'PAG'
        LOOP
            -- Paso 1: Insertar en documentos_cancelaciones
			IF v_cartera_encabezado.vlr_cuentas_orden > 0 THEN
	            INSERT INTO documentos_cancelaciones (
	                tdocto, id_proveedor, fecha_docto, cliente, contabilizado, con_nota_credito, moneda,
	                vlr_pago_efectivo, vlr_pago_cheque, vlr_descuento, usuario_crea, vlr_pago_otros, observaciones
	            ) VALUES (
	                v_sigla_documento, v_cartera_encabezado.nro_docto, v_now, v_cliente_id, 'N', 1, 1,
	                COALESCE(p_efectivo, 0), COALESCE(p_cheque, 0), 0, v_usuario, COALESCE(p_valor_abonar, 0), NULL
	            );

				--RAISE NOTICE 'Insert en documentos_cancelaciones_detalles realizada';

	            -- Paso 2: Insertar en documentos_cancelaciones_detalles
	            SELECT COALESCE(MAX(consecutivo), 0) + 1 INTO v_consecutivo
	            FROM documentos_cancelaciones_detalles
	            WHERE numero_documento = v_cartera_encabezado.nro_docto;

	            -- Paso 3: Generar liquidaciones
				-- Iterar sobre los resultados de la función generar_liquidacion(9543)
			    FOR v_liquidacion IN
				    SELECT id, nro_docto, nro_cuota, descripcion, prioridad,
				           vlr_detalle, vlr_cuentas_orden, con_descuento
				    FROM generar_liquidacion(v_cartera_encabezado.nro_docto)
				LOOP
				    -- Validar si vlr_cuentas_orden es mayor a 0 y la cuota es la que se está procesando
				    IF v_liquidacion.vlr_cuentas_orden > 0 THEN

				        INSERT INTO documentos_cancelaciones_detalles (
				            tipo_documento, numero_documento, consecutivo, cliente_id,
				            tipo_pago, tipo_documento_dvt, numero_documento_dvt,
				            numero_cuota_dvt, consecutivo_dvt, concepto_descuento_dvt,
				            valor_pago, valor_descuento, servicio_concepto_lcd, tipo_recalculo
				        ) VALUES (
				            v_sigla_documento, v_cartera_encabezado.nro_docto, v_consecutivo,
				            v_cliente_id, 'DVT', 'PAG', v_liquidacion.nro_docto,
				            v_liquidacion.nro_cuota, 1, v_liquidacion.con_descuento,
				            v_liquidacion.vlr_cuentas_orden, 0, 1, 'N'
				        );

				        --RAISE NOTICE 'Insert en documentos_cancelaciones_detalles realizada';

				        -- Incrementar el consecutivo para la siguiente inserción
				        v_consecutivo := v_consecutivo + 1;
				    END IF;
				END LOOP;

	            -- Paso 4: Generar contabilidad
	            INSERT INTO comprobantes (fecha_comprobante, tercero_id, tipo_documento_contables_id, n_documento, descripcion_comprobante, estado, usuario_original)
	            VALUES (v_now, (select id from terceros where tercero_id = p_cliente_id::VARCHAR), p_tipo_documento_id, v_numerador, 'Cancelacion cliente ' || v_cliente_id || ' ' || (SELECT nombres FROM terceros WHERE tercero_id = p_cliente_id::varchar), 'Activo', v_usuario)
	            RETURNING id INTO v_comprobante_id;

	            -- Paso 5: Crear líneas de comprobante
	            FOR v_cuota_detalle IN
				    SELECT * FROM documentos_cancelaciones_detalles WHERE numero_documento = v_cartera_encabezado.nro_docto
				LOOP
				    v_cuenta_contable :=
				        CASE
				            WHEN v_cuota_detalle.concepto_descuento_dvt IS NOT NULL THEN
				                valida_cuenta_contable(v_cuota_detalle.concepto_descuento_dvt, v_cuenta_capital)
				            WHEN v_cuota_detalle.concepto_descuento_vde IS NOT NULL THEN
				                valida_cuenta_contable(v_cuota_detalle.concepto_descuento_vde, v_cuenta_capital)
				            WHEN v_cuota_detalle.concepto_descuento_lcd IS NOT NULL THEN
				                valida_cuenta_contable(v_cuota_detalle.concepto_descuento_lcd, v_cuenta_capital)
				            ELSE
				                NULL -- O un valor por defecto si ninguna variable tiene valor
				        END;

				    IF v_cuenta_contable IS NOT NULL THEN
				        INSERT INTO comprobante_lineas (pucs_id, tercero_id, descripcion_linea, debito, credito, comprobante_id, linea)
				        VALUES (
				            v_cuenta_contable,
				            (SELECT id FROM terceros WHERE tercero_id = p_cliente_id::VARCHAR),
				            (SELECT descripcion FROM concepto_descuentos WHERE codigo_descuento = COALESCE(v_cuota_detalle.concepto_descuento_dvt, v_cuota_detalle.concepto_descuento_vde, v_cuota_detalle.concepto_descuento_lcd)),
				            0,
				            v_cuota_detalle.valor_pago,
				            v_comprobante_id,
				            (SELECT COALESCE(MAX(linea), 0) + 1 FROM comprobante_lineas WHERE comprobante_id = v_comprobante_id)
				        );
				    END IF;
				END LOOP;

				-- Paso 6: Crear la contrapartida del comprobante
				INSERT INTO comprobante_lineas (pucs_id, tercero_id, descripcion_linea, debito, credito, comprobante_id, linea)
				VALUES (
				    (SELECT id FROM pucs WHERE puc = p_tipo_pago), -- Obtener el ID del PUC para el tipo de pago
				    (SELECT id FROM terceros WHERE tercero_id = p_cliente_id::VARCHAR),
				    'Cuenta por cobrar cliente ' || p_cliente_id || ' ' || (SELECT nombres FROM terceros WHERE tercero_id = p_cliente_id::VARCHAR),
				    (SELECT SUM(valor_pago) FROM documentos_cancelaciones_detalles WHERE numero_documento = v_cartera_encabezado.nro_docto), -- Suma de todas las líneas anteriores
				    0,
				    v_comprobante_id,
				    (SELECT COALESCE(MAX(linea), 0) + 1 FROM comprobante_lineas WHERE comprobante_id = v_comprobante_id)
				);

	            -- Paso 6: Actualizar cuotas detalles
				FOR v_cuota_detalle IN
				    SELECT * FROM cuotas_detalles WHERE tdocto = 'PAG' AND nro_docto = v_cartera_encabezado.nro_docto AND estado = 'A'
				LOOP
					BEGIN
						v_campo_abono := CASE WHEN v_sigla_documento = 'NCR' THEN 'vlr_abono_ncr' ELSE 'vlr_abono_rec' END;

						UPDATE cuotas_detalles
						SET vlr_abono_ncr = CASE WHEN v_campo_abono = 'vlr_abono_ncr' THEN v_cuota_detalle.vlr_cuentas_orden ELSE vlr_abono_ncr END,
							vlr_abono_rec = CASE WHEN v_campo_abono = 'vlr_abono_rec' THEN v_cuota_detalle.vlr_cuentas_orden ELSE vlr_abono_rec END
						WHERE id = v_cuota_detalle.id;

						-- Primero, actualiza los valores de las columnas vlr_abono_ncr y vlr_abono_rec
						UPDATE cuotas_encabezados
						SET vlr_abono_ncr = (
								SELECT COALESCE(SUM(vlr_abono_ncr), 0)
								FROM cuotas_detalles
								WHERE nro_docto = v_cartera_encabezado.nro_docto
								AND nro_cuota = v_cuota_detalle.nro_cuota
							),
							vlr_abono_rec = (
								SELECT COALESCE(SUM(vlr_abono_rec), 0)
								FROM cuotas_detalles
								WHERE nro_docto = v_cartera_encabezado.nro_docto
								AND nro_cuota = v_cuota_detalle.nro_cuota
							)
						WHERE nro_docto = v_cartera_encabezado.nro_docto
						AND nro_cuota = v_cuota_detalle.nro_cuota;

						-- Luego, realiza la validación y actualiza el estado si la sumatoria es igual a vlr_cuota
						UPDATE cuotas_encabezados
						SET estado = 'C', fecha_pago_total = v_now
						WHERE nro_docto = v_cartera_encabezado.nro_docto
						AND nro_cuota = v_cuota_detalle.nro_cuota
						AND (vlr_abono_ncr + vlr_abono_rec + vlr_abono_dpa + vlr_descuento) = vlr_cuota;

						-- Solo validar cancelación si hubo abono en esta cuota
						v_sumatoria := v_cuota_detalle.vlr_abono_ncr + v_cuota_detalle.vlr_abono_rec + v_cuota_detalle.vlr_abono_dpa + v_cuota_detalle.vlr_descuento + v_cuota_detalle.vlr_cuentas_orden ;

						IF v_sumatoria = v_cuota_detalle.vlr_detalle THEN
							UPDATE cuotas_detalles
							SET estado = 'C', fecha_pago_total = v_now, vlr_cuentas_orden = 0.00
							WHERE id = v_cuota_detalle.id;
						END IF;

						RAISE NOTICE 'Actualice cuota_detalle %',v_sumatoria;
					END;
				END LOOP;

				RAISE NOTICE 'Actualice cuotas_encabezados';

				-- Paso : Actualizar cuotas detalles
				UPDATE cartera_encabezados
				SET vlr_abono_ncr = (
				        SELECT COALESCE(SUM(vlr_abono_ncr), 0)
				        FROM cuotas_detalles
				        WHERE nro_docto = v_cartera_encabezado.nro_docto and con_descuento = 1
				    ),
				    vlr_abono_rec = (
				        SELECT COALESCE(SUM(vlr_abono_rec), 0)
				        FROM cuotas_detalles
				        WHERE nro_docto = v_cartera_encabezado.nro_docto and con_descuento = 1
				    )
				WHERE nro_docto = v_cartera_encabezado.nro_docto and estado = 'A';

				UPDATE cartera_encabezados
				SET
					estado = 'C',
					fecha_pago_total = NOW(),
					vlr_saldo_actual = vlr_saldo_actual - (COALESCE(vlr_abono_ncr, 0) + COALESCE(vlr_abono_rec, 0) + COALESCE(vlr_abono_dpa, 0) + COALESCE(vlr_reliquidado, 0)),
					vlr_cuentas_orden = 0.00,
					vlr_congelada = 0.00
				WHERE
					nro_docto = v_cartera_encabezado.nro_docto -- Reemplaza con el valor correcto
					AND (COALESCE(vlr_abono_ncr, 0) + COALESCE(vlr_abono_rec, 0) + COALESCE(vlr_abono_dpa, 0) + COALESCE(vlr_reliquidado, 0)) = vlr_docto_vto;
			END IF;

			-- Opcion 2 y 3
			IF v_cartera_encabezado.vlr_congelada > 0 THEN

				-- Paso 1: Insertar en documentos_cancelaciones
	            INSERT INTO documentos_cancelaciones (
	                tdocto, id_proveedor, fecha_docto, cliente, contabilizado, con_nota_credito, moneda,
	                vlr_pago_efectivo, vlr_pago_cheque, vlr_descuento, usuario_crea, vlr_pago_otros, observaciones
	            ) VALUES (
	                v_sigla_documento, v_cartera_encabezado.nro_docto, v_now, v_cliente_id, 'N', 1, 1,
	                COALESCE(p_efectivo, 0), COALESCE(p_cheque, 0), 0, v_usuario, COALESCE(p_valor_abonar, 0), NULL
	            );

	            -- Paso 2: Insertar en documentos_cancelaciones_detalles
	            SELECT COALESCE(MAX(consecutivo), 0) + 1 INTO v_consecutivo
	            FROM documentos_cancelaciones_detalles
	            WHERE numero_documento = v_cartera_encabezado.nro_docto;

				RAISE NOTICE 'Tome el ultimo consecutivo';

                -- Procesar créditos vigentes
                INSERT INTO documentos_cancelaciones_detalles (
                    tipo_documento, numero_documento, consecutivo, cliente_id, tipo_pago, tipo_documento_dre,
                    numero_documento_dre, valor_pago, valor_descuento, servicio_concepto_lcd, tipo_recalculo, concepto_descuento_lcd
                ) VALUES (
                    v_sigla_documento, v_cartera_encabezado.nro_docto, v_consecutivo, v_cliente_id, 'DRE', 'PAG',
                    v_cartera_encabezado.nro_docto, v_cartera_encabezado.vlr_congelada, 0, 1, 'C', 1
                );

				RAISE NOTICE 'Agregue registro en documentos_cancelaciones_detalles para opcion 2 0 3';

                UPDATE cartera_encabezados
                SET vlr_reliquidado = vlr_reliquidado + v_cartera_encabezado.vlr_congelada
                WHERE id = v_cartera_encabezado.id;

				RAISE NOTICE 'Actualice vlr_reliquidado para opcion 2 0 3';

                -- Procesar obligaciones
                FOR v_obligacion IN
                    SELECT * FROM detalle_vencimiento_descuento WHERE cliente = v_cliente_id AND estado = 'A'
                LOOP
					IF v_obligacion.vlr_congelada > 0 THEN
	                    INSERT INTO documentos_cancelaciones_detalles (
	                        tipo_documento, numero_documento, consecutivo, cliente_id, tipo_pago, concepto_descuento_vde,
	                        consecutivo_vde, numero_cuota_vde, valor_pago, valor_descuento, servicio_concepto_lcd
	                    ) VALUES (
	                        v_sigla_documento, v_cartera_encabezado.nro_docto, v_consecutivo, v_cliente_id, 'VDE',
	                        v_obligacion.con_descuento, v_obligacion.consecutivo, v_obligacion.nro_cuota, v_obligacion.vlr_congelada, 0, 1
	                    );

						INSERT INTO registros_detalle_descuentos (
							cliente, con_descuento, linea, con_servicio, fecha, tdocto, nro_docto, vlr_debito, vlr_credito
						) VALUES (
							v_cliente_id, v_obligacion.con_descuento, (SELECT linea FROM registros_detalle_descuentos WHERE cliente = v_cliente_id ORDER BY linea DESC LIMIT 1) + 1,
							17, v_now, v_sigla_documento, v_cartera_encabezado.nro_docto, 0, v_obligacion.vlr_congelada
						);

						UPDATE saldos_descuentos
						SET saldo_credito = saldo_credito + v_obligacion.vlr_congelada, saldo_total = saldo_credito + v_obligacion.vlr_congelada - saldo_debito
						WHERE cliente = v_cliente_id AND con_descuento = v_obligacion.con_descuento AND amo = 9999;

	                    UPDATE detalle_vencimiento_descuento
	                    SET abono_cuota = v_obligacion.vlr_congelada, vlr_congelada = 0.00
	                    WHERE id = v_obligacion.id;
					END IF;
                END LOOP;

                -- Procesar otros conceptos
                FOR v_otro_concepto IN
                    SELECT * FROM tmp_vencimiento_descuento WHERE cliente = v_cliente_id
                LOOP
                    INSERT INTO documentos_cancelaciones_detalles (
                        tipo_documento, numero_documento, consecutivo, cliente_id, tipo_pago, concepto_descuento_lcd,
                        valor_pago, valor_descuento, servicio_concepto_lcd
                    ) VALUES (
                        v_sigla_documento, v_cartera_encabezado.nro_docto, v_consecutivo, v_cliente_id, 'LCD',
                        v_otro_concepto.codigo_concepto, v_otro_concepto.valor, 0, 1
                    );

                    DELETE FROM tmp_vencimiento_descuento WHERE id = v_otro_concepto.id;
                END LOOP;

				RAISE NOTICE 'Finalize para opcion 2 0 3';

				UPDATE cartera_encabezados
				SET
					estado = 'C',
					fecha_pago_total = NOW(),
					vlr_saldo_actual = vlr_saldo_actual - (COALESCE(vlr_abono_ncr, 0) + COALESCE(vlr_abono_rec, 0) + COALESCE(vlr_abono_dpa, 0) + COALESCE(vlr_reliquidado, 0)),
					vlr_cuentas_orden = 0.00,
					vlr_congelada = 0.00
				WHERE
					nro_docto = v_cartera_encabezado.nro_docto -- Reemplaza con el valor correcto
					AND (COALESCE(vlr_abono_ncr, 0) + COALESCE(vlr_abono_rec, 0) + COALESCE(vlr_abono_dpa, 0) + COALESCE(vlr_reliquidado, 0)) = vlr_docto_vto;

				UPDATE detalle_vencimiento_descuento
				SET estado = 'C', fecha_pago_total = v_now
				WHERE vlr_cuota = abono_cuota AND cliente = v_cliente_id AND estado = 'A';

				IF v_cartera_encabezado.vlr_saldo_actual > 0 THEN
					CALL generar_cuotas_y_detalles(v_cartera_encabezado.nro_docto);
				END IF;
            END IF;
	      END LOOP;

	        -- Notificación de finalizado
	        RAISE NOTICE 'Comprobante generado correctamente';
    EXCEPTION
        WHEN OTHERS THEN
            RAISE EXCEPTION 'Error al generar el comprobante: %', SQLERRM;
    END;
END;
$BODY$;

ALTER FUNCTION public.generar_comprobante_v2(integer, integer, text, numeric, numeric, numeric)
    OWNER TO postgres;



-- Índices para la tabla 'terceros'
CREATE INDEX idx_terceros_tercero_id ON terceros(tercero_id);

-- Índices para la tabla 'tipo_documento_contables'
CREATE INDEX idx_tipo_documento_contables_sigla ON tipo_documento_contables(sigla);
CREATE INDEX idx_tipo_documento_contables_numerador ON tipo_documento_contables(numerador);

-- Índices para la tabla 'pucs'
CREATE INDEX idx_pucs_puc ON pucs(puc);

-- Índices para la tabla 'cartera_encabezados'
CREATE INDEX idx_cartera_encabezados_nro_docto ON cartera_encabezados(nro_docto);
CREATE INDEX idx_cartera_encabezados_cliente ON cartera_encabezados(cliente);
CREATE INDEX idx_cartera_encabezados_estado ON cartera_encabezados(estado);
CREATE INDEX idx_cartera_encabezados_tdocto ON cartera_encabezados(tdocto);

-- Índices para la tabla 'documentos_cancelaciones'
CREATE INDEX idx_documentos_cancelaciones_numero_documento ON documentos_cancelaciones(cliente);

-- Índices para la tabla 'documentos_cancelaciones_detalles'
CREATE INDEX idx_documentos_cancelaciones_detalles_numero_documento ON documentos_cancelaciones_detalles(numero_documento);

-- Índices para la tabla 'cuotas_detalles'
CREATE INDEX idx_cuotas_detalles_tdocto ON cuotas_detalles(tdocto);
CREATE INDEX idx_cuotas_detalles_nro_docto ON cuotas_detalles(nro_docto);

-- Índices para la tabla 'cuotas_encabezados'
CREATE INDEX idx_cuotas_encabezados_nro_docto ON cuotas_encabezados(nro_docto);
