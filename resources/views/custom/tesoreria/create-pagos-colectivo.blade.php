@php
    use Illuminate\Support\Facades\DB;
    $conceptos = DB::table('concepto_descuentos')
        ->select('id', 'codigo_descuento', 'descripcion', 'cuenta_contable')
        ->whereIn('identificador_concepto', ['DS', 'AP', 'HC'])
        ->get()
        ->toArray();

    $obligaciones = $this->cliente->obligaciones ?? [];

    $vencimientoDescuentos = $this->cliente->vencimientoDescuentos ?? [];

@endphp

<x-filament-panels::page>
    <style>
        .div-loading {
            height: 18vh;
        }

        .payment-loader {
            width: 150px;
            position: absolute;
            top: 50%;
            left: 50%;
            -webkit-transform: translateY(-50%) translateX(-50%);
            -moz-transform: translateY(-50%) translateX(-50%);
            -o-transform: translateY(-50%) translateX(-50%);
            transform: translateY(-50%) translateX(-50%);
        }

        .payment-loader .binding {
            content: '';
            width: 60px;
            height: 4px;
            border: 2px solid #00c4bd;
            margin: 0 auto;
        }

        .payment-loader .pad {
            width: 80px;
            height: 50px;
            border-radius: 8px;
            border: 2px solid #00c4bd;
            padding: 6px;
            margin: 0 auto;
        }

        .payment-loader .chip {
            width: 12px;
            height: 8px;
            background: #00c4bd;
            border-radius: 3px;
            margin-top: 4px;
            margin-left: 3px;
        }

        .payment-loader .line {
            width: 52px;
            margin-top: 6px;
            margin-left: 3px;
            height: 4px;
            background: #00c4bd;
            border-radius: 100px;
            opacity: 0;
            -webkit-animation: writeline 3s infinite ease-in;
            -moz-animation: writeline 3s infinite ease-in;
            -o-animation: writeline 3s infinite ease-in;
            animation: writeline 3s infinite ease-in;
        }

        .payment-loader .line2 {
            width: 32px;
            margin-top: 6px;
            margin-left: 3px;
            height: 4px;
            background: #00c4bd;
            border-radius: 100px;
            opacity: 0;
            -webkit-animation: writeline2 3s infinite ease-in;
            -moz-animation: writeline2 3s infinite ease-in;
            -o-animation: writeline2 3s infinite ease-in;
            animation: writeline2 3s infinite ease-in;
        }

        .payment-loader .line:first-child {
            margin-top: 0;
        }

        .payment-loader .line.line1 {
            -webkit-animation-delay: 0s;
            -moz-animation-delay: 0s;
            -o-animation-delay: 0s;
            animation-delay: 0s;
        }

        .payment-loader .line.line2 {
            -webkit-animation-delay: 0.5s;
            -moz-animation-delay: 0.5s;
            -o-animation-delay: 0.5s;
            animation-delay: 0.5s;
        }

        .payment-loader .loader-text {
            text-align: center;
            margin-top: 20px;
            font-size: 16px;
            line-height: 16px;
            color: #5f6571;
            font-weight: bold;
        }


        @keyframes writeline {
            0% {
                width: 0px;
                opacity: 0;
            }

            33% {
                width: 52px;
                opacity: 1;
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        @keyframes writeline2 {
            0% {
                width: 0px;
                opacity: 0;
            }

            33% {
                width: 32px;
                opacity: 1;
            }

            70% {
                opacity: 1;
            }

            100% {
                opacity: 0;
            }
        }

        /* Estilo general para la tabla */
        .table {
            width: 100%;
            /* Asegúrate de que la tabla ocupe todo el ancho disponible */
            border-collapse: collapse;
            /* Colapsar bordes para un mejor aspecto */
        }

        /* Estilo para las celdas de la tabla */
        .table th,
        .table td {
            border: 1px solid #ddd;
            /* Bordes de las celdas */
            padding: 5px;
            width: 0.1rem;
            /* Espaciado interno */
            font-size: 15px;
            /* Ajustar el tamaño de la fuente */
            text-align: right;
            /* Alinear texto a la izquierda */
        }

        /* Estilo para el encabezado de la tabla */
        .table th {
            background-color: #f2f2f2;
            /* Color de fondo para el encabezado */
            font-weight: bold;
            /* Negrita para el encabezado */
        }

        /* Ajustar el ancho de columnas específicas si es necesario */
        .table .col-1 {
            width: 30%;
            /* Ancho específico para la primera columna */
        }

        .table .col-2 {
            width: 50%;
            /* Ancho específico para la segunda columna */
        }

        .table .col-3 {
            width: 20%;
            /* Ancho específico para la tercera columna */
        }
    </style>
    <x-filament-panels::form>
        {{ $this->form }}

        <hr>

        @if ($this->show)
            <div x-data="data()" class="disabled" x-init="cedula = '{{ $this->cliente->tercero_id }}'">


                <div>
                    <div class="flex justify-end mt-2">
                        <x-filament::button
                            @click="generarComprobante({{ $this->total_a_pagar }}, {{ $this->tipo_documento_id ?? 0 }}, {{ $this->tipo_pago ?? 0 }}, {{ $this->pendiente ?? 0 }}, '{{ $this->sigla_documento }}')"
                            icon="heroicon-m-plus">
                            Guardar comprobante
                        </x-filament::button>
                    </div>
                </div>

                {{-- Informacion del cliente --}}
                <div class="tabs mt-2">
                    <x-filament::tabs label="Content tabs">
                        <x-filament::tabs.item alpine-active="creditos"
                            @click="creditos = true, obligaciones = false, otros_conceptos = false">
                            Creditos vigentes
                        </x-filament::tabs.item>

                        <x-filament::tabs.item alpine-active="obligaciones"
                            @click="obligaciones = true, creditos = false, otros_conceptos = false">
                            Obligaciones servicios
                        </x-filament::tabs.item>

                        <x-filament::tabs.item alpine-active="otros_conceptos"
                            @click="otros_conceptos = true, creditos = false, obligaciones = false">
                            Otros conceptos
                        </x-filament::tabs.item>
                    </x-filament::tabs>
                </div>

                {{-- Creditos vigentes --}}
                <div x-show="creditos">
                    <div
                        class="overflow-hidden mt-6 w-full overflow-x-auto rounded-md border border-neutral-300 dark:border-neutral-700">
                        <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                            <thead
                                class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                                <tr>
                                    <th scope="col" class="p-4">Nro docto</th>
                                    <th scope="col" class="p-4">Nro cuotas</th>
                                    <th scope="col" class="p-4">Linea credito</th>
                                    <th scope="col" class="p-4">Saldo actual</th>
                                    <th scope="col" class="p-4">Valor a aplicar</th>
                                    <th scope="col" class="p-4">Descuento</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                                @forelse ($this->cliente->carteraEncabezados->where('estado', 'A')->where('tdocto', 'PAG') as $index => $credito)
                                    <tr @click="updatedSelectedCredito({{ $credito->nro_docto }}, {{ $credito->vlr_saldo_actual }}, {{ $this->efectivo }}, {{ $this->cheque }}, {{ $this->valor_abonar }})"
                                        class="cursor-pointer">
                                        <td class="p-4">{{ $credito->nro_docto }}</td>
                                        <td class="p-4">{{ $credito->nro_cuotas }}</td>
                                        <td class="p-4">{{ $credito->lineaCredito->descripcion ?? 'N/A' }}</td>
                                        <td class="p-4">{{ $credito->vlr_saldo_actual }}</td>
                                        <td class="p-4">
                                            {{ $credito->vlr_congelada == 0 ? $credito->vlr_cuentas_orden : $credito->vlr_congelada }}
                                        </td>
                                        <td class="p-4"></td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center p-8">No hay creditos vigentes</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Obligaciones servicios --}}
                <div x-show="obligaciones">
                    <div
                        class="overflow-hidden w-full mt-6 overflow-x-auto rounded-md border border-neutral-300 dark:border-neutral-700">
                        <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                            <thead
                                class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                                <tr>
                                    <th scope="col" class="p-4">Fecha vencimiento</th>
                                    <th scope="col" class="p-4">Con descuento</th>
                                    <th scope="col" class="p-4">Descripcion descuento</th>
                                    <th scope="col" class="p-4">Consecutivo</th>
                                    <th scope="col" class="p-4">Nro cuota</th>
                                    <th scope="col" class="p-4">Valor cuota</th>
                                    <th scope="col" class="p-4">Valor aplicar</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                                @forelse ($this->cliente->obligaciones as $obligacion)
                                    <tr>
                                        <td class="p-4"> {{ $obligacion->fecha_vencimiento }}</td>
                                        <td class="p-4">{{ $obligacion->con_descuento }}</td>
                                        <td class="p-4">{{ $obligacion->descripcion_concepto }}</td>
                                        <td class="p-4">{{ $obligacion->consecutivo }}</td>
                                        <td class="p-4">{{ $obligacion->nro_cuota }}</td>
                                        <td class="p-4">{{ number_format($obligacion->vlr_cuota, 2) }}</td>
                                        <td class="p-4" @dblclick="toggleEditingState({{ $obligacion->id }})">

                                            <span
                                                x-show="!isEditing[{{ $obligacion->id }}]">{{ number_format($obligacion->vlr_congelada, 2) }}</span>

                                            <input type="number" x-show="isEditing[{{ $obligacion->id }}]"
                                                x-trap="isEditing[{{ $obligacion->id }}]"
                                                @click.away="disableEditing({{ $obligacion->id }})"
                                                @keydown.enter="aplicaValoraTotal(editingValues[{{ $obligacion->id }}], {{ $obligacion->vlr_cuota }}, {{ $obligacion->id }}),disableEditing({{ $obligacion->id }})"
                                                @keydown.window.escape="disableEditing({{ $obligacion->id }})"
                                                class="bg-white focus:outline-none focus:shadow-outline border border-gray-300 rounded-lg py-2 px-4 appearance-none leading-normal w-128"
                                                :class="{ 'border-red-500': invalidValues[{{ $obligacion->id }}] }"
                                                x-ref="{{ $obligacion->id }}-class"
                                                x-model="editingValues[{{ $obligacion->id }}]">
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center p-8">No hay obligaciones</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                {{-- Otros conceptos --}}
                <div x-show="otros_conceptos" class="grid mt-6 grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        Concepto
                        <x-filament::input.wrapper>
                            <x-slot name="prefix" icon="heroicon-c-magnifying-glass-circle">
                            </x-slot>
                            <x-filament::input.select x-model="selectedConcepto" @change="updateForm(selectedConcepto)">
                                <option value="">Seleccionar</option>
                                <template x-for="(concepto, index) in conceptos" :key="concepto.id">
                                    <option :value="concepto.id"
                                        x-text="concepto.codigo_descuento + ' ' + concepto.descripcion"></option>
                                </template>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div class="mb-3">
                        Cuenta contable
                        <x-filament::input.wrapper>
                            <x-filament::input type="text" readonly x-model="newRow.cuenta_contable" />
                        </x-filament::input.wrapper>
                    </div>

                    <div class="mb-3">
                        Valor
                        <x-filament::input.wrapper>
                            <x-filament::input @keydown.enter="addComposicion()" type="text" x-model="newRow.valor" />
                        </x-filament::input.wrapper>
                    </div>

                    <div class="mb-3">
                        <label for=""></label>
                        <x-filament::button icon="heroicon-m-plus" @click="addComposicion()">
                            Agregar
                        </x-filament::button>
                    </div>
                </div>

                <div x-show="otros_conceptos" class="table_conceptos">
                    <div
                        class="overflow-hidden w-full mt-6 overflow-x-auto rounded-md border border-neutral-300 dark:border-neutral-700">
                        <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                            <thead
                                class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                                <tr>
                                    <th scope="col" class="p-4">Codigo Concepto</th>
                                    <th scope="col" class="p-4">Descripción</th>
                                    <th scope="col" class="p-4">Valor</th>
                                    <th scope="col" class="p-4">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                                @forelse ($this->cliente->vencimientoDescuentos as $row)
                                    <tr>
                                        <td class="p-4">{{ $row->codigo_concepto }}</td>
                                        <td class="p-4">{{ $row->descripcion }}</td>
                                        <td class="p-4">{{ $row->valor }}</td>
                                        <td class="p-4" style="justify-items: center;">
                                            <x-filament::icon-button icon="heroicon-m-trash" color="danger"
                                                @click="eliminaVencimientoDescuento({{ $row->id }})" />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center p-8">No hay descuentos</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>

                <x-filament::modal id="type-pay">
                    <x-slot name="heading">
                        Seleccione el tipo de anticipo
                    </x-slot>

                    <div class="flex flex-col gap-2">
                        <div
                            class="flex items-center justify-start gap-2 font-medium text-neutral-600 has-[:disabled]:opacity-75 dark:text-neutral-300">
                            <input id="radioMac" type="radio"
                                class="before:content[''] relative h-4 w-4 appearance-none rounded-full border border-neutral-300 bg-neutral-50 before:invisible before:absolute before:left-1/2 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-neutral-100 checked:border-black checked:bg-black checked:before:visible focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-neutral-800 checked:focus:outline-black disabled:cursor-not-allowed dark:border-neutral-700 dark:bg-neutral-900 dark:before:bg-black dark:checked:border-white dark:checked:bg-white dark:focus:outline-neutral-300 dark:checked:focus:outline-white"
                                name="radioDefault" value="" @click="OnDisabled = true, this.valorInteres = 0">
                            <label for="radioMac" class="text-sm">Abono anticipado</label>
                        </div>
                        <br>
                        <div
                            class="flex items-center justify-start gap-2 font-medium text-neutral-600 has-[:disabled]:opacity-75 dark:text-neutral-300">
                            <input id="radioWindows" type="radio"
                                class="before:content[''] relative h-4 w-4 appearance-none rounded-full border border-neutral-300 bg-neutral-50 before:invisible before:absolute before:left-1/2 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-neutral-100 checked:border-black checked:bg-black checked:before:visible focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-neutral-800 checked:focus:outline-black disabled:cursor-not-allowed dark:border-neutral-700 dark:bg-neutral-900 dark:before:bg-black dark:checked:border-white dark:checked:bg-white dark:focus:outline-neutral-300 dark:checked:focus:outline-white"
                                name="radioDefault" value="" @click="OnDisabled = false, calcularIntereses()">
                            <label for="radioWindows" class="text-sm">Compra plazo</label>
                        </div>
                        <br>
                        <div
                            class="flex items-center justify-start gap-2 font-medium text-neutral-600 has-[:disabled]:opacity-75 dark:text-neutral-300">
                            <input id="radioLinux" type="radio"
                                class="before:content[''] relative h-4 w-4 appearance-none rounded-full border border-neutral-300 bg-neutral-50 before:invisible before:absolute before:left-1/2 before:top-1/2 before:h-1.5 before:w-1.5 before:-translate-x-1/2 before:-translate-y-1/2 before:rounded-full before:bg-neutral-100 checked:border-black checked:bg-black checked:before:visible focus:outline focus:outline-2 focus:outline-offset-2 focus:outline-neutral-800 checked:focus:outline-black disabled:cursor-not-allowed dark:border-neutral-700 dark:bg-neutral-900 dark:before:bg-black dark:checked:border-white dark:checked:bg-white dark:focus:outline-neutral-300 dark:checked:focus:outline-white"
                                name="radioDefault" value="" @click="OnDisabled = false, calcularIntereses()">
                            <label for="radioLinux" class="text-sm">Reliquida cuota</label>

                            <div
                                class="flex w-[20%] float-end max-w-xs flex-col gap-1 text-neutral-600 dark:text-neutral-300">
                                <input id="textInputDefault" type="number"
                                    class="w-full rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black disabled:cursor-not-allowed disabled:opacity-75 dark:border-neutral-700 dark:bg-neutral-900/50 dark:focus-visible:outline-white"
                                    placeholder="0.00" x-model="valorInteres" :disabled="OnDisabled" />
                            </div>

                        </div>

                        <br>
                        <hr>

                        <div class="flex w-full max-w-xs flex-col gap-1 text-neutral-600 dark:text-neutral-300">
                            <label for="textInputDefault" class="w-fit pl-0.5 text-sm">Valor anticipado</label>
                            <input id="textInputDefault" type="number" :disabled="OnDisabled"
                                class="w-full rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-black disabled:cursor-not-allowed disabled:opacity-75 dark:border-neutral-700 dark:bg-neutral-900/50 dark:focus-visible:outline-white"
                                placeholder="0.00" x-model="valorAplicado" />
                        </div>
                    </div>



                    <x-slot name="footer">
                        <x-filament::button @click="aplicarValor" style="display: flex; margin: auto;">
                            Aplicar
                        </x-filament::button>
                    </x-slot>


                </x-filament::modal>

                <x-filament::modal id="modal_liquidacion" class="overflow-auto" width="7xl">
                    <x-slot name="heading">
                        Liquidación de credito
                    </x-slot>

                    <x-filament::loading-indicator class="h-10 w-10 flex m-auto" x-show="loading" />

                    @php
                        $valor_total =
                            floatval($this->efectivo) + floatval($this->cheque) + floatval($this->valor_abonar) ?? 0;
                    @endphp

                    <div class="flex gap-4 justify-center" x-show="!loading">
                        <div class="flex flex-col">
                            <label for="valorDocumento" class="pl-0.5 text-sm">Valor Documento</label>
                            <input id="valorDocumento" type="text"
                                class="rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm"
                                name="valorDocumento" placeholder="Ingrese el valor del documento" autocomplete="off"
                                value="{{ $valor_total }}" readonly />
                        </div>

                        <div class="flex flex-col">
                            <label for="valorPorAplicar" class="pl-0.5 text-sm">Valor por Aplicar</label>
                            <input id="valorPorAplicar" type="text"
                                class="rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm"
                                name="valorPorAplicar" placeholder="Ingrese el valor por aplicar" autocomplete="off"
                                readonly />
                        </div>

                        <div class="flex flex-col">
                            <label for="valorAPagar" class="pl-0.5 text-sm">Valor a Pagar</label>
                            <input id="valorAPagar" type="text"
                                class="rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm"
                                name="valorAPagar" placeholder="Ingrese el valor a pagar" autocomplete="off"
                                x-model="valorApagar" readonly />
                        </div>

                        <div class="flex flex-col">
                            <label for="valorDescuento" class="pl-0.5 text-sm">Valor Descuento</label>
                            <input id="valorDescuento" type="text"
                                class="rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm"
                                name="valorDescuento" disabled placeholder="Ingrese el valor de descuento"
                                autocomplete="off" />
                        </div>

                        <div class="flex flex-col">
                            <label for="valorAAplicar" class="pl-0.5 text-sm">Valor a Aplicar</label>
                            <input id="valorAAplicar" type="text"
                                class="rounded-md border border-neutral-300 bg-neutral-50 px-2 py-2 text-sm"
                                name="valorAAplicar" placeholder="Ingrese el valor a aplicar" autocomplete="off"
                                x-model="valorAAplicar" @keyup.enter="distribuirValor" />
                        </div>
                    </div>

                    <div class="overflow-hidden w-full mt-6 overflow-x-auto rounded-md border border-neutral-300 dark:border-neutral-700"
                        x-show="!loading">

                        <table class="w-full text-left text-sm text-neutral-600 dark:text-neutral-300">
                            <thead
                                class="border-b border-neutral-300 bg-neutral-50 text-sm text-neutral-900 dark:border-neutral-700 dark:bg-neutral-900 dark:text-white">
                                <tr>
                                    <th scope="col" class="p-4">Nro docto</th>
                                    <th scope="col" class="p-4">Nro cuota</th>
                                    <th scope="col" class="p-4">Descripción</th>
                                    <th scope="col" class="p-4">Prioridad</th>
                                    <th scope="col" class="p-4">Valor detalle</th>
                                    <th scope="col" class="p-4">Valor aplicar</th>
                                    <th scope="col" class="p-4">Valor descuento</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-neutral-300 dark:divide-neutral-700">
                                <template x-for="(row, index) in liquidaciones" :key="index">
                                    <tr>
                                        <td class="p-4" x-text="row.nro_docto"></td>
                                        <td class="p-4" x-text="row.nro_cuota"></td>
                                        <td class="p-4" x-text="row.descripcion"></td>
                                        <td class="p-4" x-text="row.prioridad"></td>
                                        <td class="p-4" x-text="parseCustomFloat(row.vlr_detalle)"></td>
                                        <td class="p-4" x-text="parseCustomFloat(row.vlr_cuentas_orden) || 0.00">
                                        </td>
                                        <td class="p-4" x-text="row.vlr_descuento || 0.00"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>



                    <x-slot name="footer">
                        <x-filament::button
                            @click="$wire.aplicarValorLiquidacion(nro_docto, obtenerSumaLiquidaciones()), $dispatch('close-modal', { id: 'modal_liquidacion' }), valorAplicado = ''"
                            style="display: flex; margin: auto;">
                            Guardar
                        </x-filament::button>
                    </x-slot>


                </x-filament::modal>

                <x-filament::modal id="modal-loading" width="md" :close-by-clicking-away="false" :close-by-escaping="false"
                    :close-button="false">

                    <div class="div-loading">
                        <div class="payment-loader">
                            <div class="pad">
                                <div class="chip"></div>
                                <div class="line line1"></div>
                                <div class="line line2"></div>
                            </div>
                            <div class="loader-text">
                                Procesando pago...
                            </div>
                        </div>
                    </div>

                </x-filament::modal>

                <x-filament::modal id="modal-error" width="md" icon="heroicon-o-exclamation-triangle"
                    icon-color="danger" width="md">
                    <x-slot name="heading">
                        Ocurrio un error al procesar el pago
                    </x-slot>

                    <x-slot name="description">
                        Ha ocurrido un error al procesar el pago, intente nuevamente.
                    </x-slot>
                </x-filament::modal>

                <x-filament::modal id="modal-success" width="md" icon="heroicon-o-check-circle"
                    icon-color="primary" width="md">
                    <x-slot name="heading">
                        El pago fue procesado con exito
                    </x-slot>

                    <template x-show="siglaDocumento == 'NCR'">
                        <x-slot name="description">
                            NCR - Nota de credito {{ $this->numerador }}
                        </x-slot>
                    </template>

                    <template x-show="siglaDocumento != 'NCR'">
                        <x-slot name="description">
                            REC - Recibo de caja {{ $this->numerador }}
                        </x-slot>
                    </template>

                    <div style="padding: 20px;">
                        <table class="table">
                            <tr>
                                <td>Efectivo apicado:</td>
                                <td>{{ number_format($this->efectivo ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Cheque aplicado:</td>
                                <td>{{ number_format(floatval($this->cheque) ?? 0, 2) }}</td>
                            </tr>
                            <tr>
                                <td>Usuario:</td>
                                <td>{{ auth()->user()->name ?? '' }}</td>
                            </tr>
                        </table>
                    </div>

                </x-filament::modal>

            </div>
        @endif
    </x-filament-panels::form>


    <script>
        function data() {
            return {
                creditos: true,
                obligaciones: false,
                otros_conceptos: false,
                OnDisabled: true,
                open: false,
                valorAplicado: '',
                message: '',
                valorApagar: '',
                showSave: false,
                pendientePorAplicar: @js($this->pendiente),
                loading: false,
                conceptos: @js($conceptos),
                vencimientoDescuentos: @js($vencimientoDescuentos),
                selectedConcepto: '',
                calculoIntereses: '',
                valorInteres: 0.00,
                valorAAplicar: 0,
                nro_docto: '',
                saldoSelected: '',
                isEditing: {},
                editingValues: {},
                invalidValues: {},
                liquidaciones: [],
                siglaDocumento: 'NCR',
                cedula: '',
                newRow: {
                    id_concepto: '',
                    cliente: '',
                    nro_docto: '',
                    codigo_concepto: '',
                    descripcion: '',
                    cuenta_contable: '',
                    valor: 0,
                },
                parseCustomFloat(text) {
                    // Si el texto es nulo, undefined o vacío, retornar 0
                    if (!text) return 0;

                    // Eliminar espacios en blanco al inicio y al final
                    text = text.trim();

                    // Reemplazar comas por puntos para manejar decimales
                    text = text.replace(',', '.');

                    // Eliminar cualquier carácter que no sea un número, un punto o un signo negativo
                    text = text.replace(/[^0-9.-]/g, '');

                    // Convertir a número flotante
                    const number = parseFloat(text);

                    // Si el resultado no es un número válido, retornar 0
                    if (isNaN(number)) return 0;

                    // Redondear a dos decimales y devolver como número
                    return parseFloat(number.toFixed(2));
                },
                updateForm(concepto, valor = null) {
                    if (concepto) {
                        this.conceptos.forEach((c) => {
                            if (c.id == concepto) {
                                this.newRow.id_concepto = c.id;
                                this.newRow.cliente = this.cedula;
                                this.newRow.nro_docto = this.nro_docto;
                                this.newRow.descripcion = c.descripcion;
                                this.newRow.codigo_concepto = c.codigo_descuento;
                                this.newRow.cuenta_contable = c.cuenta_contable;
                                this.newRow.valor = valor || 0;
                            }
                        });
                    }
                },
                distribuirValor() {

                    if (this.valorApagar < this.valorAAplicar) {

                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('Valor aplicado es mayor al valor a pagar')
                            .warning()
                            .duration(5000)
                            .send();

                        return;
                    }

                    let valorRestante = parseFloat(this.valorAAplicar);

                    this.liquidaciones.forEach(row => {
                        if (valorRestante <= 0) return;

                        let maxAplicar = row.vlr_detalle - (row.vlr_aplicar || 0);
                        let valorAplicar = Math.min(maxAplicar, valorRestante);

                        row.vlr_aplicar = (row.vlr_aplicar || 0) + valorAplicar;
                        valorRestante -= valorAplicar;
                    });

                    this.$wire.aplicaValorLiquidacion(this.nro_docto, this.liquidaciones);

                    this.$nextTick(() => {
                        this.loading = true;
                        this.$wire.generarLiquidacion(this.nro_docto).then((res) => {
                            this.liquidaciones = res;
                            this.valorApagar = res.map((v) => parseFloat(v.vlr_detalle)).reduce((a, b) =>
                                a + b, 0);
                            this.loading = false;
                        }).catch((error) => {
                            this.loading = false;
                            console.error("Error:", error);
                        });
                    });
                },
                addComposicion(row = null) {
                    console.log(row);
                    console.log(this.newRow);
                    if (row) {
                        this.vencimientoDescuentos.push({
                            ...row
                        });
                    } else {
                        this.vencimientoDescuentos.push({
                            ...this.newRow
                        });
                        this.$wire.vencimientoDescuento(this.newRow);
                        this.newRow = {
                            id_concepto: '',
                            cliente: '',
                            nro_docto: '',
                            codigo_concepto: '',
                            descripcion: '',
                            cuenta_contable: '',
                            valor: 0,
                        };
                    }
                },
                eliminaVencimientoDescuento(vencimientoDescuento) {
                    this.$wire.eliminaVencimiento(vencimientoDescuento);
                },
                toggleEditingState(id) {
                    this.isEditing[id] = !this.isEditing[id];

                    // Si no existe un valor previo, inicializamos el valor editado con un texto vacío
                    if (!this.editingValues[id]) {
                        this.editingValues[id] = '';
                    }

                    if (this.isEditing[id]) {
                        this.$nextTick(() => {
                            const inputRef = this.$refs[`${id}-class`]; // Accede directamente al ref
                            const tdElement = document.getElementById(`${id}-td`); // Busca el <td> por ID

                            if (inputRef && tdElement) {
                                tdElement.innerText = inputRef.value; // Almacena el valor actual en el <td>
                            }
                        });
                    }
                },
                validateAndDisableEditing(id, maxValue) {
                    const valor = parseFloat(this.editingValues[id]?.replace(/,/g, '') || 0);

                    if (valor > maxValue) {
                        this.invalidValues[id] = false; // Limpia el error
                        this.isEditing[id] = false; // Desactiva edición
                        console.log(`Valor válido guardado para la fila ${id}: ${valor}`);
                        // Aquí puedes llamar a un método Livewire o Axios para guardar el valor
                        // Ejemplo: $wire.saveValue(id, valor);
                    } else {
                        this.invalidValues[id] = true; // Marca como inválido
                    }
                },
                disableEditing(id) {
                    this.isEditing[id] = false; // Desactiva la edición para la fila correspondiente
                    const valorEditado = this.editingValues[id];
                },
                updatedSelectedCredito(documento, saldoActual, efectivo, cheque, valor_abonar) {
                    console.log(efectivo, cheque, valor_abonar);
                    if ((!efectivo || efectivo <= 0) && (!cheque || cheque <= 0) && ((!valor_abonar || valor_abonar <=
                            0))) {
                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('No se puede proceder sin valor válido en EFECTIVO, CHEQUE o VALOR A ABONAR')
                            .warning()
                            .duration(5000)
                            .send();
                        return;
                    } else {
                        this.nro_docto = documento;
                        this.saldoSelected = saldoActual;
                        this.$wire.nroDoctoActual(documento);
                        this.$dispatch('open-modal', {
                            id: 'type-pay'
                        })
                    }
                },
                calcularIntereses() {
                    this.$wire.calcularIntereses(this.nro_docto).then((res) => {
                        this.calculoIntereses = res;
                        this.valorInteres = parseFloat(res.interes_mora.replace(/,/g, ''));
                    });
                },
                aplicarValor() {
                    // Asegúrate de que valorAplicado sea un número válido
                    const valorAplicadoFloat = parseFloat(this.valorAplicado);

                    if (this.OnDisabled) {

                        this.loading = true;
                        this.$wire.generarLiquidacion(this.nro_docto).then((res) => {
                            this.liquidaciones = res;
                            this.valorApagar = res.map((v) => parseFloat(v.vlr_detalle)).reduce((a, b) => a + b, 0);
                            this.loading = false;

                            this.valorAplicado = '';
                            this.valorInteres = '';
                        }).catch((error) => {
                            this.loading = false;
                            console.error("Error:", error);
                        });

                        this.$dispatch('close-modal', {
                            id: 'type-pay'
                        })

                        this.$dispatch('open-modal', {
                            id: 'modal_liquidacion'
                        })
                    }

                    if (valorAplicadoFloat > this.saldoSelected) {
                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('El valor aplicado es mayor al saldo actual')
                            .warning()
                            .duration(5000)
                            .send();

                        return;
                    }

                    if (!isNaN(valorAplicadoFloat)) {
                        this.$wire.aplicarValor(this.nro_docto, valorAplicadoFloat)
                            .then((res) => {
                                //console.log(res);

                                this.conceptos.forEach((c) => {
                                    if (c.codigo_descuento == 9) {

                                        const newRow = {
                                            id_concepto: c.id,
                                            cliente: this.cedula,
                                            nro_docto: this.nro_docto,
                                            descripcion: c.descripcion,
                                            codigo_concepto: c.codigo_descuento,
                                            cuenta_contable: c.cuenta_contable,
                                            valor: this.valorInteres
                                        };

                                        // Buscar si ya existe un registro con el mismo codigo_descuento en el arreglo "rows"
                                        const index = this.vencimientoDescuentos.findIndex(item => item
                                            .codigo_concepto === c
                                            .codigo_descuento);

                                        if (index !== -1) {
                                            // Si existe, reemplazarlo
                                            this.vencimientoDescuentos[index] = newRow;
                                        } else {
                                            // Si no existe, agregarlo
                                            this.vencimientoDescuentos.push(newRow);
                                        }

                                        this.$wire.vencimientoDescuento(newRow);

                                    }
                                });

                                this.valorAplicado = '';
                                this.valorInteres = '';

                                this.$dispatch('close-modal', {
                                    id: 'type-pay'
                                })
                            })
                            .catch((error) => {
                                console.error("Error al aplicar valor:", error);
                            });
                    } else {
                        console.error("El valor aplicado no es un número válido:", this.valorAplicado);
                    }
                },
                aplicaValoraTotal(aplica, vlr_cuota, id) {
                    if (aplica > vlr_cuota) {
                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('El valor aplicado no puede ser mayor al valor de la cuota')
                            .warning()
                            .duration(5000)
                            .send();

                        this.editingValues[id] = 0;
                        aplica = 0
                        return;
                    }
                    this.$wire.updateValorAplicado(aplica, id);
                },
                obtenerSumaLiquidaciones() {
                    if (this.valorAAplicar > 0) {
                        let suma = 0;
                        this.liquidaciones.forEach(element => {
                            // Asegúrate de que el valor sea convertido a número antes de sumarlo
                            suma += parseFloat(element.vlr_cuentas_orden) || 0;
                            console.log(element);
                        });
                        this.valorAplicado = suma;
                        return suma;
                    }

                    this.valorAAplicar = 0; // Resetear el input después de distribuir
                    return 0;
                },
                generarComprobante(total_a_pagar, tipo_documento_id, tipo_pago, pendiente, sigla) {
                    console.log(total_a_pagar, tipo_documento_id, tipo_pago, sigla);
                    this.siglaDocumento = sigla;
                    if (tipo_documento_id == 0) {
                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('Debe seleccionar un tipo de documento')
                            .warning()
                            .duration(5000)
                            .send();
                        return;
                    } else if (tipo_pago == 0) {
                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('Debe seleccionar un tipo de pago')
                            .warning()
                            .duration(5000)
                            .send();
                        return;
                    } else if (total_a_pagar > 0 && pendiente === 0) {
                        this.$wire.generarComprobante();
                        this.$dispatch('open-modal', {
                            id: 'modal-loading'
                        });
                    } else {
                        new FilamentNotification()
                            .title('Atención')
                            .icon('heroicon-m-exclamation-circle')
                            .body('No se puede generar el comprobante, cuando el valor aplicable es diferente al total a pagar')
                            .warning()
                            .duration(5000)
                            .send();
                        return;
                    }

                }
            }
        }
    </script>
</x-filament-panels::page>
