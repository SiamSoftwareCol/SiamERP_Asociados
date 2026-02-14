<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InformacionFinanciera extends Model
{
    use HasFactory;

    protected $table = 'informacion_financieras';

    protected $fillable = [
        'total_activos',
        'total_pasivos',
        'total_patrimonio',
        'salario',
        'servicios',
        'otros_ingresos',
        'total_ingresos',
        'gastos_sostenimiento',
        'gastos_financieros',
        'arriendos',
        'otros_gastos',
        'gastos_personales',
        'total_ingresos',
    ];

    public function Tercero(): BelongsTo
    {
        return $this->belongsTo(Tercero::class);
    }
}
