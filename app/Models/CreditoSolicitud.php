<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditoSolicitud extends Model
{
    use HasFactory;

    protected $table = 'credito_solicitudes';

    protected $fillable = [
        'linea',
        'descripcion',
        'clasificacion',
        'tipo_garantia',
        'tipo_inversion',
        'moneda',
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
        'asociado_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $numerador = TipoDocumentoContable::where('sigla', 'SOL')->first();
            $model->solicitud = $numerador->numerador;
            $numerador->increment('numerador');
            $numerador->save();
        });
    }

    public function terceroAsociado()
    {

        return $this->belongsTo(\App\Models\Tercero::class, 'asociado', 'tercero_id');
    }


    public function asociado()
    {
        return $this->belongsTo(Asociado::class, 'asociado_id');
    }

    public function lineaCredito()
    {
        return $this->belongsTo(CreditoLinea::class, 'linea', 'id');
    }


        public function tasa()
    {
        return $this->belongsTo(Tasa::class, 'descripcion', 'id');
    }

    public function empresaCredito()
    {
        return $this->belongsTo(Pagaduria::class, 'empresa');
    }


        public function garantias()
        {
            return $this->hasMany(Garantia::class, 'numero_documento_garantia'); // Ajusta los nombres de tabla/llave según tu DB
        }

    public function cuotasEncabezados()
    {
        return $this->hasManyThrough(
            CuotaEncabezado::class,
            PlanDesembolso::class,
            'solicitud_id',
            'nro_docto',
            'id',
            'nro_documento_vto_enc'
        );
    }
}
