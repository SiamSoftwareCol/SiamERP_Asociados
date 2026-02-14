<?php

namespace App\Filament\Pages;

use App\Services\DateService;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class GestionFechaProceso extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string    $navigationIcon = 'heroicon-o-calendar-days';
    protected static string     $view = 'filament.pages.gestion-fecha-proceso';
    protected static ?string    $navigationGroup = 'Configuración General';
    protected static ?int       $navigationSort = 0;
        protected static ?string    $navigationLabel = 'Fechas Actual Sistema';
        protected static ?string    $modelLabel = 'Fechas Actual Sistema';
        protected static ?string    $pluralModelLabel = 'Fechas Actual Sistema';
        protected static ?string    $slug = 'Par/Tab/FechaSistema';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'fecha_proceso' => app(DateService::class)->get()->toDateString(),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                DatePicker::make('fecha_proceso')
                    ->label('Fecha Actual del Sistema')
                    ->helperText('Esta fecha se usará para todos los cálculos y procesos.'),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Guardar Fecha')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $nuevaFecha = $this->form->getState()['fecha_proceso'];

        // Obtenemos una instancia fresca del servicio aquí
        app(DateService::class)->set(Carbon::parse($nuevaFecha));

        Notification::make()->title('Fecha del sistema actualizada.')->success()->send();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reset')
                ->label('Volver a Fecha Real')
                ->color('danger')
                ->requiresConfirmation()
                ->action(function() {
                    // Obtenemos una instancia fresca del servicio también aquí
                    $dateService = app(DateService::class);
                    $dateService->reset();

                    Notification::make()->title('El sistema ahora usa la fecha real.')->success()->send();
                    $this->mount();
                }),
        ];
    }
}
