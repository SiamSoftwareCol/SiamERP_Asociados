<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\InformeSaldosCdat;
use App\Jobs\ProcessCausacionCdat;
use App\Jobs\ProcessGeneracionPagosCdat;
use App\Models\LogProcesoCdat;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables;

class ProcesosMensualesCdat extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string    $navigationIcon = 'heroicon-o-cog';
    protected static string     $view = 'filament.pages.procesos-mensuales-cdat';
    protected static ?string    $title = 'Procesos Mensuales de CDAT';
    protected static ?string    $cluster = InformeSaldosCdat::class;
    protected static ?string    $slug = 'Par/Tab/ProcMensCdat';
    protected static ?int       $navigationSort = 5;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('causarIntereses')
                ->label('1. Causar Intereses')
                ->color('info')
                ->icon('heroicon-o-calculator')
                ->form([
                    Forms\Components\DatePicker::make('fecha_corte')
                        ->label('Fecha de Corte del Mes')
                        ->required()
                        ->default(now()->subMonthNoOverflow()->endOfMonth())
                ])
                ->action(function (array $data) {
                    ProcessCausacionCdat::dispatch($data['fecha_corte'], auth()->user());
                    Notification::make()->title('Proceso de causación iniciado.')->success()->send();
                }),

            Action::make('generarPagos')
                ->label('2. Generar Cuentas por Pagar')
                ->color('success')
                ->icon('heroicon-o-currency-dollar')
                ->requiresConfirmation()
                ->action(function () {
                    ProcessGeneracionPagosCdat::dispatch(auth()->user());
                    Notification::make()->title('Generación de pagos iniciada.')->success()->send();
                }),
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(LogProcesoCdat::query())
            ->columns([
                Tables\Columns\TextColumn::make('periodo_proceso')->date('F Y')->sortable()->label('Período'),
                Tables\Columns\TextColumn::make('tipo_proceso')->badge()->formatStateUsing(fn ($state) => str_replace('_', ' ', $state))->color(fn ($state) => match ($state) { 'CAUSACION_INTERES' => 'info', 'GENERACION_PAGO' => 'success', default => 'gray', }),
                Tables\Columns\TextColumn::make('user.name')->label('Ejecutado Por'),
                Tables\Columns\IconColumn::make('estado')->icon(fn ($state) => match ($state) { 'INICIADO' => 'heroicon-o-clock', 'COMPLETADO' => 'heroicon-o-check-circle', 'FALLIDO' => 'heroicon-o-x-circle', })->color(fn ($state) => match ($state) { 'INICIADO' => 'warning', 'COMPLETADO' => 'success', 'FALLIDO' => 'danger', }),
                Tables\Columns\TextColumn::make('detalles.mensaje')->label('Resultado')->default('N/A'),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y h:i A')->label('Fecha Ejecución'),
            ])
            ->defaultSort('created_at', 'desc')
            ->groups([
                Tables\Grouping\Group::make('periodo_proceso')->date()->label('Mes del Proceso')->getTitleFromRecordUsing(fn ($record) => $record->periodo_proceso->format('Y F')),
            ]);
    }
}
