<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;


class CdatPlazo extends Model
{
        use HasFactory;

    protected $fillable = [
        'nombre',
        'dias',
        'descripcion',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'dias' => 'integer',
    ];



    public function cdatTiposComoPrincipal(): HasMany
    {
        return $this->hasMany(CdatTipo::class, 'cdat_plazo_id');
    }
}
