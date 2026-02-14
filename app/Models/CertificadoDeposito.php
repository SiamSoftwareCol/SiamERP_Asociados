<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CertificadoDeposito extends Model
{
    use HasFactory;


    protected $table = 'cdats';


    protected $fillable = [
        'numero_cdat',
        'user_id',
        'titular_id',
        'valor',
        'plazo',
        'tasa_interes',
        'tasa_ea',
        'fecha_creacion',
        'fecha_ultima_renovacion',
        'fecha_vencimiento',
        'estado',
        'intereses_generados',
        'contabilizado',
        'fecha_cancelacion',
        'valor_retencion',
        'observaciones',
        'linea_captacion',
        'retencion',
        'medio_constitucion',
        'pago_intereses',

    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_vencimiento' => 'datetime',
    ];

    public function asociado()
    {
        return $this->belongsTo(Asociado::class, 'titular_id', 'codigo_interno_pag');
    }

    public function beneficiarios()
    {
        return $this->hasMany(Beneficiario::class, 'cdat_id');
    }

    public function getValorEnLetrasAttribute()
    {
        return $this->numeroALetras($this->valor);
    }

    private function numeroALetras($numero)
    {
        $formatter = new \NumberFormatter('es_CO', \NumberFormatter::SPELLOUT);
        $valorEnLetras = strtoupper($formatter->format($numero));

        return $valorEnLetras . ' PESOS M/CTE';
    }

    public function causaciones(): HasMany
    {

        return $this->hasMany(CausacionInteresCdat::class, 'cdat_id');
    }


    public function pagos(): HasMany
    {
        return $this->hasMany(PagoInteresCdat::class, 'cdat_id');
    }

    public function cdatTipo()
    {
        return $this->belongsTo(CdatTipo::class, 'linea_captacion');
    }
}
