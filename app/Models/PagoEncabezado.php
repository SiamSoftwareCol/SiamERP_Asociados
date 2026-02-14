<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PagoEncabezado extends Model
{
    protected $fillable = ['tdocto', 'nro_docto', 'fecha_docto', 'cliente', 'vlr_pago_efectivo', 'vlr_pago_cheque', 'vlr_pago_otros', 'usuario_crea', 'estado'];

    protected $table = 'pago_encabezados';

    public function detalles()
    {
        return $this->hasMany(PagoDetalle::class);
    }


}
