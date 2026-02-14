<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CausacionInteresCdat extends Model
{
    //

        public function cdat()
    {
        return $this->belongsTo(CertificadoDeposito::class);
    }
}
