<?php

namespace App\Models;

use Filament\Panel;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Althinect\FilamentSpatieRolesPermissions\Concerns\HasSuperAdmin;
use Afsakar\FilamentOtpLogin\Models\Contracts\CanLoginDirectly;



class User extends Authenticatable implements FilamentUser, CanLoginDirectly

{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use HasSuperAdmin;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'canview',
        'username'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function canAccessPanel(\Filament\Panel $panel): bool
{
    // 1. Si el usuario es Administrador
    if ($this->canview === 'administracion') {
        // Permitir acceso si el panel es 'admin'
        return $panel->getId() === 'admin';
    }

    // 2. Si el usuario es Asociado
    if ($this->canview === 'asociados') {
        // Permitir acceso si el panel es 'asociado' Y tiene vinculado un asociado_id
        return $panel->getId() === 'asociado' && !is_null($this->asociado_id);
    }

    // 3. Por seguridad, cualquier otro caso no entra
    return false;
}



    public function canLoginDirectly(): bool
    {
        return str($this->email)->endsWith('@fondep.com');
    }





        public function asociado()
    {
        return $this->belongsTo(Asociado::class, 'asociado_id', 'codigo_interno_pag');
    }
}
