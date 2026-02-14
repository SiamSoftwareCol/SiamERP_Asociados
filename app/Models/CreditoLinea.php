<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditoLinea extends Model
{
    use HasFactory;
    protected $table = 'credito_lineas';

    protected $fillable = [
        'descripcion',
        'clasificacion_id',
        'tipo_garantia_id',
        'tipo_inversion_id',
        'moneda_id',
        'periodo_pago',
        'interes_cte',
        'interes_mora',
        'tipo_cuota',
        'tipo_tasa',
        'nro_cuotas_max',
        'nro_cuotas_gracia',
        'cant_gar_real',
        'cant_gar_pers',
        'monto_min',
        'monto_max',
        'abonos_extra',
        'ciius',
        'subcentro',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $latest_record = CreditoLinea::orderBy('created_at', 'DESC')->first();
            $record_number = $latest_record ? $latest_record->id : 1;

            $model->linea = str_pad($record_number + 1, 8, '0', STR_PAD_LEFT);
        });
    }

    public function moneda(): BelongsTo
    {
        return $this->belongsTo(Moneda::class, 'moneda_id');
    }

    public function clasificacion(): BelongsTo
    {
        return $this->belongsTo(ClasificacionCredito::class, 'clasificacion_id');
    }

    public function tipoInversion(): BelongsTo
    {
        return $this->belongsTo(TipoInversion::class, 'tipo_inversion_id');
    }

    public function tipoGarantia(): BelongsTo
    {
        return $this->belongsTo(TipoGarantia::class, 'tipo_garantia_id');
    }

    public function periodoPago()
    {
        return $this->belongsTo(periodoPago::class);
    }

    public function creditoSolicitudes()
    {
        return $this->hasMany(CreditoSolicitud::class, 'linea', 'id');
    }

    public function subcentro(): BelongsTo
    {
        return $this->belongsTo(Subcentro::class, 'subcentro_id');
    }

}
