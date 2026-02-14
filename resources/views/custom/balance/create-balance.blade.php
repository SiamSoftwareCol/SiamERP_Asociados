<meta name="csrf-token" content="{{ csrf_token() }}">

<div>
    <h2 class="text-center text-xl font-bold mb-4 mt-6">Generar Reporte Balance General</h2>
    <div class="flex gap-8 mt-6">
        <div class="flex-none w-72">
            <ul class="hidden flex-col gap-y-7 md:flex m-5">
                <form id="form_data">
                    <div class="mb-6 mt-3">
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Tipo de
                            Balance</label>

                        <x-filament::input.wrapper>
                            <x-filament::input.select id="tipo_balance" name="tipo_balance" required>
                                <option value="0" selected disabled>Seleccionar Tipo de Balance</option>
                                <option value="1">Balance General - Situacion Financiera </option>
                                <option value="2">Balance Horizontal</option>
                                <option value="3">Balance por Tercero</option>
                                <option value="4">Balance Comparativo</option>
                            </x-filament::input.select>
                        </x-filament::input.wrapper>
                    </div>

                    <div class="mb-6 mt-3">
                        <label for="fecha_inicial"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha Inicial</label>
                        <x-filament::input.wrapper>
                            <x-filament::input id="fecha_inicial" name="fecha_inicial" type="date" required />
                        </x-filament::input.wrapper>
                    </div>

                    <div class="mb-6 mt-3">
                        <label for="fecha_final"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Fecha Final</label>
                        <x-filament::input.wrapper>
                            <x-filament::input id="fecha_final" name="fecha_final" type="date" required />
                        </x-filament::input.wrapper>
                    </div>

                    <div class="mb-6 mt-3">
                        <label for="is_13_month"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">¿Incluye Mes
                            Trece?</label>
                        <label class="p-5">
                            <x-filament::input.checkbox id="is_13_month" name="is_13_month" />
                        </label>
                    </div>


                    <div class="mb-6 mt-3">
                        <label for="nivel"
                            class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Nivel</label>
                        <x-filament::input.wrapper>
                            <x-filament::input id="nivel" name="nivel" type="number" required  value="7"/>
                        </x-filament::input.wrapper>
                    </div>

                </form>

                <button style="--c-400:var(--primary-400);--c-500:var(--primary-500);--c-600:var(--primary-600);"
                    class="fi-btn relative grid-flow-col items-center justify-center font-semibold outline-none transition duration-75 focus-visible:ring-2 pointer-events-none opacity-70 rounded-lg fi-color-custom fi-btn-color-primary fi-size-sm fi-btn-size-sm gap-1 px-2.5 py-1.5 text-sm inline-grid shadow-sm bg-custom-600 text-white hover:bg-custom-500 focus-visible:ring-custom-500/50 dark:bg-custom-500 dark:hover:bg-custom-400 dark:focus-visible:ring-custom-400/50"
                    disabled type="button" wire:loading.attr="disabled" id="generar">
                    <span class="fi-btn-label">
                        Generar reporte
                    </span>
                </button>

                <div id="export_button" style="display: contents;">

                </div>
            </ul>
        </div>
        <div
            class="flex-1 mt-6 border border-dashed border-gray-300 rounded-lg flex items-center justify-center relative">

            <embed id="pdf" type="application/pdf" width="100%" height="600px" class="rounded-lg hidden" />

            <div id="divEmpty">
                <x-filament::icon id="empty" icon="heroicon-m-document-text"
                    class="h-20 w-20 text-gray-500 dark:text-gray-400" />
            </div>

            <x-filament::loading-indicator id="loading" class="h-20 w-20 hidden" />
        </div>
    </div>


    <script src="{{ asset('js/lib/jquery.min.js') }}"></script>

<script>
    $(document).ready(function() {
        $.ajaxSetup({
            headers: {
                // Esto asegura que jQuery siempre envíe el token en el encabezado
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content") || "{{ csrf_token() }}"
            }
        });

        var e = $("#form_data"),
            a = $("#generar"),
            n = $("#pdf"),
            t = $("#empty"),
            o = $("#loading"),
            r = $("#tipo_balance"),
            i = $("#fecha_inicial"),
            s = $("#fecha_final"),
            c = $("#is_13_month"),
            l = $("#nivel"),
            d = $("#export_button");

        // Escucha cambios para habilitar/deshabilitar el botón
        e.on("input change", function() {
            var allFilled = !0;
            e.find("input[required], select[required]").each(function() {
                if ($(this).val() === "" || $(this).val() === null || $(this).val() === "0") {
                    allFilled = !1;
                }
            });

            if (allFilled) {
                a.prop("disabled", !1).removeClass("pointer-events-none opacity-70");
            } else {
                a.prop("disabled", !0).addClass("pointer-events-none opacity-70");
            }
        });

        a.on("click", function() {
            o.removeClass("hidden");
            t.addClass("hidden");
            n.addClass("hidden");

            // Incluimos el token directamente en los datos del envío
            var dataToSend = {
                _token: "{{ csrf_token() }}",
                tipo_balance: r.val(),
                fecha_inicial: i.val(),
                fecha_final: s.val(),
                is_13_month: c.is(":checked"),
                nivel: l.val()
            };

            var routeUrl = "{{ route('generarpdf') }}";
            switch (dataToSend.tipo_balance) {
                case "2": routeUrl = "{{ route('generar.balance.horizontal') }}"; break;
                case "3": routeUrl = "{{ route('generar.balance.tercero') }}"; break;
                case "4": routeUrl = "{{ route('generar.balance.comparativo') }}"; break;
            }

            f(routeUrl, dataToSend);

            function f(url, payload) {
                $(".button_export_excel").remove();
                $.ajax({
                    url: url,
                    type: "POST",
                    data: payload,
                    success: function(response) {
                        if (response.pdf) n.attr("src", "data:application/pdf;base64," + response.pdf);

                        if (response.excel) {
                            var blob = new Blob([new Uint8Array(atob(response.excel).split("").map(function(char) {
                                return char.charCodeAt(0);
                            }))], { type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" });

                            d.append(`<x-filament::button color="info" class="button_export_excel" style="width: 100%;" href="${URL.createObjectURL(blob)}" tag="a" download="${response.excel_file_name}" icon="heroicon-o-document-arrow-down" icon-position="after"> Exportar Excel </x-filament::button>`);

                            new FilamentNotification().title("Excel listo para exportar").success().send();
                        }
                        o.addClass("hidden");
                        n.removeClass("hidden");
                    },
                    error: function(xhr) {
                        var msg = (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Error inesperado";
                        new FilamentNotification().title(msg).danger().send();
                        o.addClass("hidden");
                        t.removeClass("hidden");
                    }
                });
            }
        });
    });
</script>
</div>
