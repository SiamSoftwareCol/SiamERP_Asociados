<?php

namespace App\Filament\Pages;

use App\Filament\Clusters\InformeSaldosCdat;
use Filament\Pages\Page;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;


class SimuladorCdat extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $cluster = InformeSaldosCdat::class;
    protected static ?string $navigationLabel = 'Simulador de CDAT';
    protected static string $view = 'filament.pages.simulador-cdat';
    protected static ?int $navigationSort = 1;

    public ?array $data = [];
    public ?array $resultados = null;

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Parámetros de la Simulación')
                    ->schema([
                        TextInput::make('valor')
                            ->label('Inversion')
                            ->numeric()
                            ->prefix('$')
                            ->inputMode('decimal')
                            ->hint('Valor Inversion')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->live(onBlur: true),
                        TextInput::make('plazo')
                            ->label('Plazo (en días)')
                            ->required()
                            ->numeric()
                            ->minValue(30)
                            ->live(onBlur: true),
                        TextInput::make('tasa_ea')
                            ->label('Tasa Efectiva Anual (%)')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->live(onBlur: true),
                        TextInput::make('tasa_retencion')
                            ->label('Tasa de Retención (%)')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->default(0)
                            ->live(onBlur: true),
                    ])->columns(4),
            ])
            ->statePath('data');
    }

    public function simular(): void
    {
        $datos = $this->form->getState();



        $valor = (float) ($datos['valor'] ?? 0);
        $plazo = (int) ($datos['plazo'] ?? 0);
        $tasaEA = (float) ($datos['tasa_ea'] ?? 0);
        $tasaRetencion = (float) ($datos['tasa_retencion'] ?? 0);

        $interesesBrutos = 0;
        if ($valor > 0 && $tasaEA > 0 && $plazo > 0) {
            $tasaEAPorcentaje = $tasaEA / 100;
            $tasaDiaria = pow(1 + $tasaEAPorcentaje, 1 / 365) - 1;
            $interesesBrutos = $valor * $tasaDiaria * $plazo;
        }

        $valorRetencion = 0;
        if ($interesesBrutos > 0 && $tasaRetencion > 0) {
            $valorRetencion = $interesesBrutos * ($tasaRetencion / 100);
        }

        $interesesNetos = $interesesBrutos - $valorRetencion;
        $valorFinal = $valor + $interesesNetos;

        $this->resultados = [
            'valor_invertido' => $valor,
            'intereses_brutos' => round($interesesBrutos, 2),
            'tasa_retencion_aplicada' => $tasaRetencion,
            'valor_retencion' => round($valorRetencion, 2),
            'intereses_netos' => round($interesesNetos, 2),
            'valor_final' => round($valorFinal, 2),
        ];

    }

    public function limpiar(): void
    {
        $this->form->fill();
        $this->resultados = null;
    }
}







