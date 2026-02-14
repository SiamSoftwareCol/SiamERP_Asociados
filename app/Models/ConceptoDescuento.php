<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConceptoDescuento extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'concepto_descuentos'; // Opcional, pero buena práctica ser explícito



    public function getFullDescriptionAttribute()
    {
        return "{$this->cuenta_contable} - {$this->descripcion}";
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigo_descuento',
        'descripcion',
        'reservado',
        'cuenta_contable',
        'genera_interes_x_pagar',
        'cuenta_interes',
        'porcentaje_interes',
        'cuenta_rete_fuente',
        'porcentaje_rete_fuente',
        'base_rete_fuente',
        'identificador_concepto',
        'revalorizacion',
        'genera_extracto',
        'genera_cruce',
        'obliga_retiro_total',
        'porcentaje_interes_ef',
    ];
}
