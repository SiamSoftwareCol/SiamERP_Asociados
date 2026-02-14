<?php

namespace App\Filament\Asociado\Resources\PerfilResource\Pages;

use App\Filament\Asociado\Resources\PerfilResource;
use App\Models\Tercero;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePerfil extends CreateRecord
{
    protected static string $resource = PerfilResource::class;

    protected static string $view = 'custom.asociados.profile';


    public function updateRecord()
    {
        Tercero::where('tercero_id', auth()->user()->asociado->codigo_interno_pag)->update([
            'nombres' => $this->data['nombres'],
            'primer_apellido' => $this->data['primer_apellido'],
            'segundo_apellido' => $this->data['segundo_apellido'],
            'direccion' => $this->data['direccion'],
            'telefono' => $this->data['nro_telefono_fijo'],
            'celular' => $this->data['nro_celular_1'],
            'email' => $this->data['email'],
            'ciudad_id' => $this->data['ciudad'],
            'barrio_id' => $this->data['barrio'],
        ]);

        Notification::make()
            ->title('Se actualizaron los datos correctamente')
            ->icon('heroicon-m-check-circle')
            ->body('Los datos fueron actualizados correctamente')
            ->success()
            ->send();
    }
}
