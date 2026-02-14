<x-filament::modal id="modal-liquidacion" width="6xl">
    <div x-data="{
    monto: 0,
    maximo: 0,
    // Obtenemos el disponible actual desde Livewire
    get disponibleGlobal() { return @js($this->pendiente); },

    async cargar(val = 0) {
        // 1. Llamamos a la previsualización primero
        this.datos = await $wire.previsualizarLiquidacion(val);

        // 2. Calculamos la deuda total de este crédito específico
        let deudaTotal = this.datos.filas.reduce((acc, fila) => acc + (fila.total_deuda || 0), 0);

        // 3. El límite real es el total de la deuda,
        // pero NO puede exceder lo que hay disponible globalmente + lo que ya escribí
        this.maximo = Math.min(deudaTotal, this.disponibleGlobal + Number(this.monto));

        // 4. Validación de seguridad visual
        if (Number(val) > this.maximo) {
            this.monto = this.maximo;
            this.cargar(this.monto);
        }
    }
}"
        class="py-2">


        <div
            class="flex flex-col md:flex-row justify-between items-end gap-4 mb-8 bg-gray-300 p-6 rounded-2xl shadow-lg border border-gray-200">

            <div class="w-full md:w-1/2 text-left">
                <label class="text-[11px] font-black text-emerald-900 uppercase tracking-[0.2em] block mb-2">
                    Monto a Aplicar
                </label>
                <div class="flex items-left gap-3 border-b-4 border-emerald-400 pb-2">
                    <span class="text-4xl font-bold text-black leading-none">$</span>
                    <input type="number" x-model="monto" max="maximo" @input.debounce.150ms="cargar(monto)"
                        class="block w-full bg-transparent border-0 text-10xl font-mono font-bold text-black p-0 outline-none placeholder-emerald-400">
                </div>
            </div>

            <div class="text-right">
                <div class="flex flex-col">
                    <span class="text-emerald-100 text-xs uppercase font-bold tracking-widest">Resumen de
                        Aplicación</span>
                    <p class="text-6xl font-black text-green" x-text="'$ ' + Number(monto).toLocaleString()"></p>
                </div>
            </div>
        </div>

        <div class="space-y-4 bg-emerald-50/50 p-4 rounded-3xl border border-emerald-100/50">
            <template x-for="c in datos.filas" :key="c.nro_cuota">
                <div class="bg-white border border-emerald-100 rounded-xl overflow-hidden shadow-sm">
                    <div class="flex flex-wrap md:flex-nowrap items-center gap-6 p-5">

                        <div class="flex-none w-24 text-center border-r border-emerald-50 pr-6">
                            <span class="text-[10px] font-bold text-emerald-600/60 uppercase block mb-1">Cuota</span>
                            <span class="text-2xl font-black text-slate-800" x-text="c.nro_cuota"></span>
                        </div>

                        <div class="flex-none w-32 border-r border-emerald-50 pr-6">
                            <span class="text-[10px] font-bold text-emerald-600/60 uppercase block mb-1">Vence</span>
                            <span class="font-mono text-sm font-bold"
                                :class="c.es_vencida ? 'text-red-600' : 'text-slate-600'" x-text="c.fecha"></span>
                        </div>

                        <div class="flex-1 grid grid-cols-2 md:grid-cols-4 gap-2">
                            <template x-for="det in c.conceptos">
                                <div class="h-10 px-3 rounded-lg border flex items-center justify-between transition-all"
                                    :class="{
                                        'bg-success-500 text-white text-[10px] p-2 rounded shadow-xl z-50': det.abono >=
                                            det.deuda && det.deuda > 0,
                                        'bg-warning-500 text-white text-[10px] p-2 rounded shadow-xl z-50 ': det.abono >
                                            0 && det.abono < det.deuda,
                                        'bg-gray-50 border-gray-200 text-gray-400': det.deuda <= 0,
                                        'bg-white border-emerald-50 text-slate-700': det.abono == 0 && det.deuda > 0
                                    }">
                                    <span class="text-[9px] font-bold uppercase truncate mr-2"
                                        x-text="det.nombre"></span>
                                    <span class="text-[10px] font-mono"
                                        x-text="'$' + Number(det.abono).toLocaleString()"></span>
                                </div>
                            </template>
                        </div>

                        <div
                            class="flex-none text-right min-w-[150px] bg-emerald-600 p-3 rounded-xl shadow-inner shadow-emerald-700/10">
                            <span class="text-[9px] font-bold text-emerald-100 uppercase block mb-1">Total Cuota</span>
                            <span class="text-2xl font-mono font-black text-black"
                                x-text="'$ ' + Number(c.total_abono).toLocaleString()"></span>
                        </div>
                    </div>

                    <div class="h-1.5 w-full bg-emerald-50">
                        <div class="h-full bg-emerald-500 transition-all duration-500"
                            :style="'width: ' + (c.total_deuda > 0 ? (c.total_abono / c.total_deuda * 100) : 0) + '%'">
                        </div>
                    </div>
                </div>
            </template>
        </div>

        <div class="mt-8 flex justify-end gap-4 border-t border-emerald-100 pt-6">
            <x-filament::button color="gray" @click="isOpen = false" size="lg" outlined>
                Cancelar
            </x-filament::button>
            <x-filament::button color="success" size="lg" icon="heroicon-m-check-badge"
                @click="$wire.confirmarLiquidacion(monto); isOpen = false"
                class="px-12 bg-emerald-600 hover:bg-emerald-500 shadow-md">
                Confirmar Aplicación
            </x-filament::button>
        </div>
    </div>
</x-filament::modal>
