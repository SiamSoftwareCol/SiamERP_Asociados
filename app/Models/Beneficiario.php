<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Beneficiario extends Model
{
    use HasFactory;

    protected $table = 'beneficiarios_cdat';

   protected $fillable = [
        'cdat_id',
        'tercero_id',
        'porcentaje',
        'principal',
    ];


    public function certificadoDeposito()
    {
        return $this->belongsTo(CertificadoDeposito::class, 'cdat_id');
    }

    public function tercero()
    {
        return $this->belongsTo(Tercero::class, 'tercero_id', 'tercero_id');
    }


    public function cdat(): BelongsTo
    {
        return $this->belongsTo(CertificadoDeposito::class, 'cdat_id');
    }

}
