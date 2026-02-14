<nav class="flex space-x-4 border-b">
    <button type="button"
        @click="tab = 'creditos'"
        :class="tab === 'creditos' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
        <x-heroicon-m-credit-card class="w-5 h-5"/>
        Créditos
    </button>

    <button type="button"
        @click="tab = 'obligaciones'"
        :class="tab === 'obligaciones' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
        <x-heroicon-m-document-text class="w-5 h-5"/>
        Otras Obligaciones
    </button>

    <button type="button"
        @click="tab = 'otros'"
        :class="tab === 'otros' ? 'border-primary-600 text-primary-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm flex items-center gap-2">
        <x-heroicon-m-plus-circle class="w-5 h-5"/>
        Pagos Voluntarios
    </button>
</nav>
