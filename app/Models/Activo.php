<?php

namespace App\Models;


use App\Models\CategoriaActivo;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class Activo extends Model
{
    public function categoria()
    {
        return $this->belongsTo(CategoriaActivo::class);
    }

    public function responsable()
    {
        return $this->belongsTo(User::class);
    }


}

