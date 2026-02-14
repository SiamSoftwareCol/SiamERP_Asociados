<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
// 1. Importa las clases necesarias para manejar eventos
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;

// 2. Implementa la interfaz "WithEvents"
class AuxiliaresExport implements FromView, WithEvents
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('excel.auxiliares', $this->data);
    }

    /**
     * 3. Registra el evento que se ejecutará después de crear la hoja.
     * Aquí es donde definimos el tamaño de las columnas.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                // Obtiene el objeto de la hoja de cálculo
                $sheet = $event->sheet->getDelegate();

                // Define un ancho fijo para la columna 'A' (Fecha)
                $sheet->getColumnDimension('A')->setWidth(12);

                // Aplica autoajuste al resto de las columnas (de la B a la F)
                foreach (range('B', 'F') as $columnID) {
                    $sheet->getColumnDimension($columnID)->setAutoSize(true);
                }
            },
        ];
    }
}
