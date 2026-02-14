<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\CdatPlazo;
use App\Models\CdatTasa;

class CdatTipo extends Model
{
        use HasFactory;

    protected $fillable = [
        'nombre',
        'descripcion',
        'codigo_producto',
        'cdat_plazo_id',
        'cdat_tasa_id',
        'valor_minimo',
        'valor_maximo',
        'permite_renovacion',
        'porcentaje_retencion_fuente_rendimientos',
        'base_minima_retencion_fuente',
        'dias_notificacion_previa_vencimiento',
        'permite_cancelacion_anticipada',
        'porcentaje_penalizacion_cancelacion_anticipada',
        'activo',
        'puc_contable',
        'puc_contable_intereses',
        'puc_contable_retencion',
        'puc_contable_causacion'
    ];

    protected $casts = [
        'permite_renovacion' => 'boolean',
        'permite_cancelacion_anticipada' => 'boolean',
        'activo' => 'boolean',
    ];

        public function plazoPrincipal(): BelongsTo
    {
        return $this->belongsTo(CdatPlazo::class, 'cdat_plazo_id');
    }

            public function tasaPrincipal(): BelongsTo
    {
        return $this->belongsTo(CdatTasa::class, 'cdat_tasa_id');
    }
}
