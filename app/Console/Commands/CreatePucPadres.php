<?php

namespace App\Console\Commands;

use App\Models\Tercero;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreatePucPadres extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-puc-padres';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Se crean los pucs padres';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        DB::transaction(function () {
            $asociados = DB::table('asociados')->get();

            foreach ($asociados as $asociado) {
                $tercero = Tercero::where('tercero_id', $asociado->codigo_interno_pag)->first();

                // Verificar si $tercero no es null antes de acceder a sus propiedades
                if ($tercero && $tercero->email && $tercero->email != null) {
                    // Crear usuario para el asociado
                    User::create([
                        'username' => $tercero->tercero_id,
                        'name' => $tercero->nombres . ' ' . $tercero->primer_apellido . ' ' . $tercero->segundo_apellido,
                        'email' => $tercero->email,
                        'password' => bcrypt('123456'),
                        'asociado_id' => $asociado->id,
                        'canview' => 'asociado',
                        'created_at' => now(),
                        'email_verified_at' => now(),
                    ]);
                }
            }
        }, 5);
    }
}
