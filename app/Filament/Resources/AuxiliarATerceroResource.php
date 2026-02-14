<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\InformesContabilidad;
use App\Filament\Resources\AuxiliarATerceroResource\Pages;
use App\Models\AuxiliarATercero;
use App\Models\Puc;
use App\Models\Tercero;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class AuxiliarATerceroResource extends Resource
{
    protected static ?string $model = AuxiliarATercero::class;
    protected static ?string $cluster = InformesContabilidad::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';
    protected static ?string $navigationLabel = 'Auxiliares';
    protected static ?string $modelLabel = 'Auxiliares';

    /** ---------------------------
     *  Formulario de creación/edición
     *  --------------------------- */
    public static function form(Form $form): Form
    {
        return $form
            ->columns(8)
            ->schema([
                Select::make('tipo_auxiliar')
                    ->label('Tipo Auxiliar')
                    ->options([
                        '1' => 'Auxiliar Tercero',
                        '2' => 'Auxiliar Detalle  Cuenta',
                        /* '3' => 'Auxiliar Consolidado Cuenta', */
                        /* '4' => 'Auxiliar tipo de documento', */
                    ])
                    ->required()
                    ->live()
                    ->columnSpan(3)
                    ->searchable(),

                Select::make('tercero_id')
                    ->label('Tercero')
                    ->native(false)
                    ->columnSpan(4)
                    ->visible(fn(Get $get) => $get('tipo_auxiliar') === '1')
                    ->required(fn(Get $get) => $get('tipo_auxiliar') === '1')
                    ->validationMessages([
                        'required' => 'Es necesario seleccionar un tercero a generar.',
                    ])
                     ->dehydrateStateUsing(fn ($state, Get $get) => $get('tipo_auxiliar') === '1' ? (int) $state : 0)
                    ->options(
                        Tercero::all()->mapWithKeys(fn($tercero) => [
                            $tercero->id => $tercero->tercero_id . ' - ' .
                                            $tercero->nombres . ' ' .
                                            $tercero->primer_apellido . ' ' .
                                            $tercero->segundo_apellido
                        ])->toArray()
                    )
                    ->searchable(['tercero_id', 'nombres', 'primer_apellido', 'segundo_apellido']),

                DatePicker::make('fecha_inicial')
                    ->label('Fecha Inicial')
                    ->format('d/m/Y')
                    ->native(false)
                    ->required()
                    ->columnSpan(2),

                DatePicker::make('fecha_final')
                    ->label('Fecha Final')
                    ->format('d/m/Y')
                    ->native(false)
                    ->required()
                    ->columnSpan(2),

                Select::make('cuenta_inicial')
                    ->label('Cuenta Inicial')
                    ->native(false)
                    ->searchable()
                    ->options(
                        Puc::where('movimiento', true)
                            ->select(DB::raw("CONCAT(puc, ' - ', descripcion) AS puc_descripcion"), 'id')
                            ->pluck('puc_descripcion', 'id')
                            ->toArray()
                    )
                    ->columnSpan(2),

                Select::make('cuenta_final')
                    ->label('Cuenta Final')
                    ->native(false)
                    ->searchable()
                    ->options(
                        Puc::where('movimiento', true)
                            ->select(DB::raw("CONCAT(puc, ' - ', descripcion) AS puc_descripcion"), 'id')
                            ->pluck('puc_descripcion', 'id')
                            ->toArray()
                    )
                    ->columnSpan(2),
            ]);
    }


    public static function table(Table $table): Table
    {
        return $table
            ->heading('Auxiliares de Contabilidad')
            ->description(
                'En esta opción podrá consultar y generar reportes detallados de los movimientos contables registrados en el sistema.                '
            )
            ->emptyStateIcon('heroicon-o-fire')
            ->emptyStateHeading('Permite filtrar por cuenta contable, tercero, período y otros criterios.')
            ->columns([
            ])
            ->filters([
            ])

            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /** ---------------------------
     *  Relaciones
     *  --------------------------- */
    public static function getRelations(): array
    {
        return [];
    }

    /** ---------------------------
     *  Páginas
     *  --------------------------- */
    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAuxiliarATerceros::route('/'),
            'create' => Pages\CreateAuxiliarATercero::route('/create'),
            'edit'   => Pages\EditAuxiliarATercero::route('/{record}/edit'),
        ];
    }
}
