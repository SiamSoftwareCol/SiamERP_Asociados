<div class="space-y-4">
    <table class="w-full text-left border-collapse bg-white dark:bg-gray-900 rounded-xl overflow-hidden shadow-sm">
        <thead>
            <tr class="bg-gray-50 dark:bg-gray-800 border-b dark:border-gray-700">
                <th class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Tipo de Garantía</th>
                <th class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200">Descripción</th>
                <th class="px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-200 text-right">Valor</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($garantias as $garantia)
                <tr>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $garantia->tipo_garantia }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400">
                        {{ $garantia->descripcion ?? 'Sin descripción' }}
                    </td>
                    <td class="px-4 py-3 text-sm text-gray-600 dark:text-gray-400 text-right font-medium">
                        ${{ number_format($garantia->valor, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="bg-gray-50 dark:bg-gray-800 font-bold">
                <td colspan="2" class="px-4 py-2 text-sm text-right">Total Garantías:</td>
                <td class="px-4 py-2 text-sm text-right text-primary-600">
                    ${{ number_format($garantias->sum('valor'), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>
