<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoDetalle extends Model
{

        protected $fillable = ['pago_encabezado_id', 'tdocto', 'nro_docto', 'vlr_pago', 'estado_pago'];

        public function encabezado()
        {
            return $this->belongsTo(PagoEncabezado::class, 'pago_encabezado_id');
        }


}
