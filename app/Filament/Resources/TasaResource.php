<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TasaResource\Pages;
use App\Models\Tasa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Filament\Clusters\ParametrosGenerales;

class TasaResource extends Resource
{
    protected static ?string    $model = Tasa::class;
    protected static ?string    $cluster = ParametrosGenerales::class;
    protected static ?string    $navigationIcon = 'heroicon-o-presentation-chart-line';
    protected static ?string    $recordTitleAttribute = 'nombre';
    protected static ?string    $navigationGroup = 'Parametros';
    protected static ?string    $navigationParentItem = 'Parametros Asociados';
    protected static ?string    $modelLabel = 'Tasas de Interes';
    protected static ?string    $pluralModelLabel = 'Tasas de Interes';
    protected static ?string    $slug = 'Par/Tab/Tasas';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Tasa')
                    ->schema([
                        Forms\Components\TextInput::make('descripcion')
                            ->label('Nombre de la Tasa')
                            ->required()
                            ->autocomplete('off')
                            ->maxLength(255)
                            ->columnSpan('full'),
                        Forms\Components\TextInput::make('tasa')
                            ->label('Tasa Nominal Anual (%)')
                            ->required()
                            ->numeric()
                            ->suffix('%')
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set) {
                                $tasaNominal = floatval($get('tasa'));
                                if ($tasaNominal > 0) {
                                    $tasaEfectiva = (pow(1 + (($tasaNominal / 100) / 12), 12) - 1) * 100;
                                    $set('nombre', round($tasaEfectiva, 4));
                                } else {
                                    $set('nombre', 0);
                                }
                            }),
                        Forms\Components\TextInput::make('nombre')
                            ->label('Tasa Efectiva Anual (%)')
                            ->numeric()
                            ->readOnly()
                            ->suffix('%')
                            ->helperText('Este valor se calcula automáticamente.'),



                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Nombre de la Tasa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Tasa Efectiva (%)')
                    ->numeric(4)
                    ->suffix('%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('tasa')
                    ->label('Tasa Nominal (%)')
                    ->numeric(2)
                    ->suffix('%')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha Creación')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    // Función para centralizar el cálculo
    protected static function calcularTasaEfectiva(Get $get, Set $set): void
    {
        $tasaNominal = floatval($get('tasa_nominal'));
        if ($tasaNominal > 0) {
            // Fórmula: E.A. = (1 + (Tasa Nominal / 12))^12 - 1
            $tasaEfectiva = (pow(1 + (($tasaNominal / 100) / 12), 12) - 1) * 100;
            $set('tasa_efectiva', round($tasaEfectiva, 4));
        } else {
            $set('tasa_efectiva', 0);
        }
    }

        protected function mutateFormDataBeforeCreate(array $data): array
        {
            // Formatear la tasa nominal de entrada a 4 decimales
            $tasaNominal = floatval($data['tasa_nominal']);
            $data['tasa_nominal'] = sprintf('%.4f', $tasaNominal);

            // Calcular y formatear la tasa efectiva a 4 decimales
            if ($tasaNominal > 0) {
                $tasaEfectiva = (pow(1 + (($tasaNominal / 100) / 12), 12) - 1) * 100;
                $data['tasa_efectiva'] = sprintf('%.4f', $tasaEfectiva);
            } else {
                $data['tasa_efectiva'] = '0.0000';
            }

            return $data;
        }

        protected function mutateFormDataBeforeSave(array $data): array
        {

            $tasaNominal = floatval($data['tasa_nominal']);
            $data['tasa_nominal'] = sprintf('%.4f', $tasaNominal);

            if ($tasaNominal > 0) {
                $tasaEfectiva = (pow(1 + (($tasaNominal / 100) / 12), 12) - 1) * 100;
                $data['tasa_efectiva'] = sprintf('%.4f', $tasaEfectiva);
            } else {
                $data['tasa_efectiva'] = '0.0000';
            }

            return $data;
        }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTasas::route('/'),
            'create' => Pages\CreateTasa::route('/create'),
            'edit' => Pages\EditTasa::route('/{record}/edit'),
        ];
    }
}
