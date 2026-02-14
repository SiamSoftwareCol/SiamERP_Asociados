<?php

namespace App\Filament\Resources\AuxiliarATerceroResource\Pages;

use App\Exports\AuxiliaresExport;
use App\Filament\Resources\AuxiliarATerceroResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use stdClass;

class CreateAuxiliarATercero extends CreateRecord
{
    protected static string $resource = AuxiliarATerceroResource::class;
    protected static string $view = 'custom.auxiliares.create';

    public function exportPDF()
    {


        $tipo = $this->data['tipo_auxiliar'];
        if (empty($this->data['fecha_inicial']) || empty($this->data['fecha_final'])) {
            Notification::make()
                ->title('Datos Incompletos')
                ->body('Debe ingresar Fecha Inicial y Fecha Final. La Fecha Inicial debe ser el día siguiente a un cierre contable registrado.')
                ->danger()
                ->send();
            return;
        }

        if ($tipo === '1' && empty($this->data['tercero_id'])) {
            // ...enviamos una notificación de error y detenemos la ejecución.
            Notification::make()
                ->title('Datos Incompletos')
                ->body('Para generar un auxiliar por tercero, es obligatorio seleccionar un tercero de la lista.')
                ->danger()
                ->send();

            return; // Detiene la función aquí.
        }
        $fechaInicial = $this->data['fecha_inicial'];
        $fechaFinal = $this->data['fecha_final'];
        $terceroId = ($tipo === '1') ? (int) $this->data['tercero_id'] : 0;
        $cuentaInicialId = $this->data['cuenta_inicial'] ?? DB::table('pucs')->orderBy('puc', 'asc')->value('id');

        // Si no se especifica una cuenta final, se busca la última disponible ordenada por PUC.
        $cuentaFinalId = $this->data['cuenta_final'] ?? DB::table('pucs')->orderBy('puc', 'desc')->value('id');

        // Ahora se pasa el rango de cuentas a TODAS las funciones relevantes.
        $data = match ($tipo) {
            '1' => $this->generateAuxiliarTercero($fechaInicial, $fechaFinal, $terceroId, $cuentaInicialId, $cuentaFinalId),
            '2' => $this->generateAuxiliarCuentas($fechaInicial, $fechaFinal, $cuentaInicialId, $cuentaFinalId),
            '3' => $this->generateAuxiliarCuentaDetalle($fechaInicial, $fechaFinal, $cuentaInicialId, $cuentaFinalId),
            '4' => $this->generateAuxiliarTipoDocumento($fechaInicial, $fechaFinal, $cuentaInicialId, $cuentaFinalId),
            default => [],
        };

        if (empty($data['cuentas'])) {
            Notification::make()
                ->title('Error al generar el reporte.')
                ->body('No se encontraron movimientos para los filtros seleccionados.')
                ->danger()
                ->send();
            return;
        }

        Notification::make()
            ->title('Reporte generado con éxito.')
            ->body('El reporte será descargado automáticamente.')
            ->success()
            ->send();

        $fileName = $this->getFileName($tipo, $data);
        return Excel::download(new AuxiliaresExport($data), $fileName);
    }

    private function getFileName(string $tipo, array $data): string
    {
        $baseName = 'auxiliares_';
        $currentDate = now()->format('Y-m-d');

        return match ($tipo) {
            '1' => "{$baseName}terceros_{$currentDate}.xlsx",
            '2' => "{$baseName}cuentas_{$currentDate}.xlsx",
            '3' => "{$baseName}detalles_cuentas_{$currentDate}.xlsx",
            '4' => "{$baseName}tipo_documento_{$currentDate}.xlsx",
            default => "{$baseName}reporte_{$currentDate}.xlsx",
        };
    }

    private function getBaseReportData(string $title, string $balanceType, string $fechaInicial, string $fechaFinal): array
    {
        return [
            'titulo' => $title,
            'nombre_compania' => 'GRUPO FINANCIERO - FONDEP',
            'nit' => '8.000.903.753',
            'tipo_balance' => $balanceType,
            'fecha_inicial' => new \DateTime($fechaInicial),
            'fecha_final' => new \DateTime($fechaFinal),
        ];
    }

    /**
     * Ordena un array de movimientos según la lógica requerida:
     * 1. Fecha
     * 2. Tipo de Comprobante
     * 3. Número de Comprobante
     * 4. Créditos primero, luego Débitos
     */
    private function sortMovements(array &$movements): void
    {
        usort($movements, function ($a, $b) {
            // 1. Ordenar por fecha
            $comparison = $a->fecha <=> $b->fecha;
            if ($comparison !== 0) return $comparison;

            // 2. Ordenar por tipo de comprobante (documento)
            $comparison = $a->documento <=> $b->documento;
            if ($comparison !== 0) return $comparison;

            // 3. Ordenar por número de comprobante
            $comparison = $a->n_documento <=> $b->n_documento;
            if ($comparison !== 0) return $comparison;

            // 4. Ordenar por créditos (primero) y luego débitos
            // Se asigna 0 a los créditos y 1 a los débitos para que los créditos aparezcan primero.
            $aType = $a->credito > 0 ? 0 : 1;
            $bType = $b->credito > 0 ? 0 : 1;

            return $aType <=> $bType;
        });
    }

    /**
     * Procesa y agrupa los movimientos, calculando saldos correctamente.
     *
     * @param object $movimientos La colección de movimientos desde la BD.
     * @param string $fechaInicial La fecha de inicio para calcular saldos.
     * @param string $groupByField El campo por el cual agrupar (p. ej., 'puc', 'documento').
     * @param bool $byTercero Si es true, busca el saldo en la tabla de terceros.
     * @return array Los datos agrupados y procesados.
     */


private function processMovements(object $movimientos, string $fechaInicial, string $groupByField, bool $byTercero = false): array
{
    $groupedData = [];
    $saldosAnteriores = [];

    foreach ($movimientos as $movimiento) {
        $groupKey = $movimiento->$groupByField;
        $puc = $movimiento->puc;

        if (!isset($groupedData[$groupKey])) {
            $groupedData[$groupKey] = [
                'movimientos' => [],
                'descripcion' => $movimiento->nombre_cuenta ?? $movimiento->descripcion_linea,
                'saldo_inicial_grupo' => 0.00,
            ];
        }

        $saldoKey = $byTercero ? "{$puc}-{$movimiento->tercero}" : $puc;

        if (!isset($saldosAnteriores[$saldoKey])) {
            $saldoInicial = $byTercero
                ? self::buscarSaldoAnteriorParaTerceros($fechaInicial, $puc, $movimiento->tercero)
                : self::buscarSaldoAnterior($fechaInicial, $puc);

            $saldosAnteriores[$saldoKey] = $saldoInicial;
            $groupedData[$groupKey]['saldo_inicial_grupo'] = $saldoInicial;
        }

        // --- INICIO DE LA CORRECCIÓN ---
        // Se asigna el saldo anterior al movimiento actual
        $movimiento->saldo_anterior = $saldosAnteriores[$saldoKey];

        // Se calcula el nuevo saldo basado en la naturaleza de la cuenta
        if (strtoupper($movimiento->naturaleza) === 'D') {
            // Naturaleza DÉBITO: Suma débitos, resta créditos.
            $movimiento->saldo_nuevo = $movimiento->saldo_anterior + $movimiento->debito - $movimiento->credito;
        } else {
            // Naturaleza CRÉDITO: Resta débitos, suma créditos.
            $movimiento->saldo_nuevo = $movimiento->saldo_anterior - $movimiento->debito + $movimiento->credito;
        }

        // Se actualiza el saldo para la siguiente iteración
        $saldosAnteriores[$saldoKey] = $movimiento->saldo_nuevo;
        // --- FIN DE LA CORRECCIÓN ---

        $groupedData[$groupKey]['movimientos'][] = $movimiento;
    }

    // El resto de la función no necesita cambios...
    uksort($groupedData, function (string $a, string $b) {
        if ($a === $b) return 0;
        if (str_starts_with($a, $b)) return 1;
        if (str_starts_with($b, $a)) return -1;
        return (int)$a <=> (int)$b;
    });

    foreach ($groupedData as &$group) {
        $this->sortMovements($group['movimientos']);
    }

    return $groupedData;
}

    public function generateAuxiliarTercero(string $fechaInicial, string $fechaFinal, int $terceroId, ?int $cuentaInicialId = null, ?int $cuentaFinalId = null): array
    {
        $movimientos = self::buscarMovimientos($fechaInicial, $fechaFinal, $terceroId, $cuentaInicialId, $cuentaFinalId);
        if ($movimientos->isEmpty()) {
            return [];
        }

        $reportData = $this->getBaseReportData('Auxiliar Tercero', 'auxiliar_tercero', $fechaInicial, $fechaFinal);
        $reportData['tercero'] = $this->createTerceroObject($movimientos->first());

        // Usar la función centralizada, indicando que el saldo es por tercero
        $reportData['cuentas'] = $this->processMovements($movimientos, $fechaInicial, 'puc', true);

        return $reportData;
    }

    private function createTerceroObject(stdClass $movimiento): stdClass
    {
        $tercero = new stdClass();
        $tercero->tercero = $movimiento->tercero;
        $tercero->tercero_nombre = $movimiento->tercero_nombre;
        $tercero->primer_apellido = $movimiento->primer_apellido;
        $tercero->segundo_apellido = $movimiento->segundo_apellido;
        return $tercero;
    }

    public function generateAuxiliarCuentas(string $fechaInicial, string $fechaFinal, ?int $cuentaInicialId = null, ?int $cuentaFinalId = null): array
    {
        $movimientos = self::buscarMovimientosCuentas($fechaInicial, $fechaFinal, $cuentaInicialId, $cuentaFinalId);
        if ($movimientos->isEmpty()) {
            return [];
        }

        $reportData = $this->getBaseReportData('Auxiliar a Cuentas', 'auxiliar_cuentas', $fechaInicial, $fechaFinal);
        // Usar la función centralizada, el saldo NO es por tercero (valor por defecto)
        $reportData['cuentas'] = $this->processMovements($movimientos, $fechaInicial, 'puc');

        return $reportData;
    }

    public function generateAuxiliarCuentaDetalle(string $fechaInicial, string $fechaFinal, ?int $cuentaInicialId = null, ?int $cuentaFinalId = null): array
    {
        $movimientos = self::buscarMovimientosCuentas($fechaInicial, $fechaFinal, $cuentaInicialId, $cuentaFinalId);
        if ($movimientos->isEmpty()) {
            return [];
        }

        // Usar la función centralizada, el saldo NO es por tercero
        $groupedData = $this->processMovements($movimientos, $fechaInicial, 'puc');
        $this->addParentAccountDetails($groupedData, $movimientos);

        $reportData = $this->getBaseReportData('Auxiliar a Cuentas Detalles', 'auxiliar_cuentas_detalles', $fechaInicial, $fechaFinal);
        $reportData['cuentas'] = $groupedData;

        return $reportData;
    }

    private function addParentAccountDetails(array &$groupedData, object $movimientos): void
    {
        $pucDetails = collect();

        foreach ($movimientos as $movimiento) {
            $pucDetails->put($movimiento->puc, $movimiento);
        }

        $pucParents = $pucDetails->pluck('puc_padre')->filter()->unique();
        $parentAccounts = DB::table('pucs')->whereIn('puc', $pucParents)->pluck('descripcion', 'puc');
        $grandParentAccounts = [];
        $greatGrandParentAccounts = [];
        $greatGreatGrandParentAccounts = [];

        $this->getParents($pucParents, $parentAccounts, $grandParentAccounts, $greatGrandParentAccounts, $greatGreatGrandParentAccounts);

        foreach ($groupedData as $puc => &$data) {
            $movimiento = $pucDetails->get($puc);
            if ($movimiento && $parentAccounts->has($movimiento->puc_padre)) {
                $data['cuenta_padre'] = $movimiento->puc_padre . ' ' . $parentAccounts[$movimiento->puc_padre];
                if (isset($grandParentAccounts[$movimiento->puc_padre])) {
                    $data['cuenta_abuelo'] = $grandParentAccounts[$movimiento->puc_padre]->puc . ' ' . $grandParentAccounts[$movimiento->puc_padre]->descripcion;
                }
                if (isset($greatGrandParentAccounts[$movimiento->puc_padre])) {
                    $data['cuenta_bisabuelo'] = $greatGrandParentAccounts[$movimiento->puc_padre]->puc . ' ' . $greatGrandParentAccounts[$movimiento->puc_padre]->descripcion;
                }
                if (isset($greatGreatGrandParentAccounts[$movimiento->puc_padre])) {
                    $data['cuenta_tatarabuelo'] = $greatGreatGrandParentAccounts[$movimiento->puc_padre]->puc . ' ' . $greatGreatGrandParentAccounts[$movimiento->puc_padre]->descripcion;
                }
            }
        }
    }

    private function getParents($pucParents, &$parentAccounts, &$grandParentAccounts, &$greatGrandParentAccounts, &$greatGreatGrandParentAccounts)
    {
        $parentPucs = $parentAccounts->keys()->toArray();
        if (empty($parentPucs)) return;

        $grandParentPucs = DB::table('pucs')->whereIn('puc', $parentPucs)->pluck('puc_padre')->filter()->unique();
        $grandParentAccounts = DB::table('pucs')->whereIn('puc', $grandParentPucs)->get()->keyBy('puc');

        $greatGrandParentPucs = $grandParentAccounts->pluck('puc_padre')->filter()->unique();
        $greatGrandParentAccounts = DB::table('pucs')->whereIn('puc', $greatGrandParentPucs)->get()->keyBy('puc');

        $greatGreatGrandParentPucs = $greatGrandParentAccounts->pluck('puc_padre')->filter()->unique();
        $greatGreatGrandParentAccounts = DB::table('pucs')->whereIn('puc', $greatGreatGrandParentPucs)->get()->keyBy('puc');
    }

    public function generateAuxiliarTipoDocumento(string $fechaInicial, string $fechaFinal, ?int $cuentaInicialId = null, ?int $cuentaFinalId = null): array
    {
        $movimientos = self::buscarMovimientosCuentas($fechaInicial, $fechaFinal, $cuentaInicialId, $cuentaFinalId);
        if ($movimientos->isEmpty()) {
            return [];
        }

        $reportData = $this->getBaseReportData('Auxiliar a Cuentas', 'auxiliar_tipo_documento', $fechaInicial, $fechaFinal);
        // Usar la función centralizada, el saldo NO es por tercero
        $reportData['cuentas'] = $this->processMovements($movimientos, $fechaInicial, 'documento');
        ksort($reportData['cuentas']);

        return $reportData;
    }

    // Se actualiza la firma para aceptar el rango de cuentas
    public static function buscarMovimientos(string $fechaInicial, string $fechaFinal, int $terceroId, ?int $cuentaInicialId = null, ?int $cuentaFinalId = null): object
    {
        $query = DB::table('comprobantes as c')
            ->select('p.puc', 'p.descripcion as nombre_cuenta', 'p.naturaleza', 'tpd.sigla as documento', 't.tercero_id as tercero', 't.nombres as tercero_nombre', 't.primer_apellido', 't.segundo_apellido', 'cl.descripcion_linea', 'cl.debito', 'cl.credito', 'c.fecha_comprobante as fecha', 'c.n_documento', 'cl.tercero_id')
            ->join('comprobante_lineas as cl', 'c.id', '=', 'cl.comprobante_id')
            ->join('terceros as t', 'cl.tercero_id', '=', 't.id')
            ->leftJoin('pucs as p', 'cl.pucs_id', '=', 'p.id')
            ->leftJoin('tipo_documento_contables as tpd', 'c.tipo_documento_contables_id', '=', 'tpd.id')
            ->where('t.id', $terceroId)
            ->whereBetween('c.fecha_comprobante', [$fechaInicial, $fechaFinal]);

        // Se añade la lógica para filtrar por el rango de cuentas
        $pucInicial = $cuentaInicialId ? DB::table('pucs')->where('id', $cuentaInicialId)->value('puc') : null;
        $pucFinal = $cuentaFinalId ? DB::table('pucs')->where('id', $cuentaFinalId)->value('puc') : null;

        if ($pucInicial && $pucFinal) {
            $query->whereBetween('p.puc', [$pucInicial, $pucFinal]);
        } elseif ($pucInicial) {
            $query->where('p.puc', '>=', $pucInicial);
        } elseif ($pucFinal) {
            $query->where('p.puc', '<=', $pucFinal);
        }

        return $query->orderBy('p.puc')
            ->orderBy('c.fecha_comprobante')
            ->get();
    }

    public static function buscarMovimientosCuentas(string $fechaInicial, string $fechaFinal, ?int $cuentaInicialId = null, ?int $cuentaFinalId = null): object
    {
        $query = DB::table('comprobantes AS c')
            ->select([
                'p.puc',
                'p.descripcion as nombre_cuenta',
                'p.naturaleza',
                'tpd.sigla AS documento',
                't.tercero_id AS tercero',
                'cl.descripcion_linea',
                'cl.debito',
                'cl.credito',
                'c.fecha_comprobante AS fecha',
                'c.n_documento',
                'p.puc_padre'
            ])
            ->join('comprobante_lineas AS cl', 'c.id', '=', 'cl.comprobante_id')
            ->leftJoin('terceros AS t', 'cl.tercero_id', '=', 't.id')
            ->leftJoin('pucs AS p', 'cl.pucs_id', '=', 'p.id')
            ->leftJoin('tipo_documento_contables AS tpd', 'c.tipo_documento_contables_id', '=', 'tpd.id')
            ->whereBetween('c.fecha_comprobante', [$fechaInicial, $fechaFinal]);

        $pucInicial = $cuentaInicialId ? DB::table('pucs')->where('id', $cuentaInicialId)->value('puc') : null;
        $pucFinal = $cuentaFinalId ? DB::table('pucs')->where('id', $cuentaFinalId)->value('puc') : null;

        if ($pucInicial && $pucFinal) {
            $query->whereBetween('p.puc', [$pucInicial, $pucFinal]);
        } elseif ($pucInicial) {
            $query->where('p.puc', '>=', $pucInicial);
        } elseif ($pucFinal) {
            $query->where('p.puc', '<=', $pucFinal);
        }

        return $query->orderBy('p.puc')
            ->orderBy('c.fecha_comprobante')
            ->get();
    }


    public static function buscarSaldoAnterior(string $fechaInicial, string $puc): string
    {
        $fecha = new \DateTime($fechaInicial);
        $fecha->modify('-1 day');

        $ano_inicial = $fecha->format('Y');
        $mes_inicial = $fecha->format('n');

        $saldo = DB::table('saldo_pucs')
            ->where('amo', $ano_inicial)
            ->where('mes', $mes_inicial)
            ->where('puc', $puc)
            ->orderBy('id', 'DESC')
            ->value('saldo');

        return $saldo ?? '0.00';
    }


    public static function buscarSaldoAnteriorParaTerceros(string $fechaInicial, string $puc, string $tercero): string
    {
        $fecha = new \DateTime($fechaInicial);
        $fecha->modify('-1 day');

        $ano_inicial = $fecha->format('Y');
        $mes_inicial = $fecha->format('n');

        $saldo = DB::table('saldo_puc_terceros')
            ->where('amo', $ano_inicial)
            ->where('mes', $mes_inicial)
            ->where('puc', $puc)
            ->where('tercero', $tercero)
            ->orderBy('id', 'DESC')
            ->value('saldo');

        return $saldo ?? '0.00';
    }
}
