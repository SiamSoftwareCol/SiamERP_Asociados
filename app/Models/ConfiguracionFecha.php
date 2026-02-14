<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionFecha extends Model
{
    protected $table = 'configuracion_fecha';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $primaryKey = 'clave';

    protected $fillable = ['clave', 'valor'];
}
