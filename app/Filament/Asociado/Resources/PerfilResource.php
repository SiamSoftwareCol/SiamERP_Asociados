<?php

namespace App\Filament\Asociado\Resources;

use App\Filament\Asociado\Resources\PerfilResource\Pages;
use App\Filament\Asociado\Resources\PerfilResource\RelationManagers;
use App\Models\Barrio;
use App\Models\Ciudad;
use App\Models\Perfil;
use App\Models\Profesion;
use App\Models\Tercero;
use App\Models\TipoIdentificacion;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PerfilResource extends Resource
{
    protected static ?string $model = Perfil::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $user = Tercero::where('tercero_id', auth()->user()->asociado->codigo_interno_pag)->first();
        return $form
            ->schema([
                Section::make('Actualización de Datos')
                    ->description('Tercero Natural')
                    ->icon('heroicon-m-user')
                    ->schema([
                        Forms\Components\TextInput::make('nro_identificacion')
                            ->label('Nro Identificación')
                            ->disabled()
                            ->default($user->tercero_id),
                        Forms\Components\TextInput::make('nombres')
                            ->label('Nombre')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->nombres),
                        Forms\Components\TextInput::make('primer_apellido')
                            ->label('Primer Apellido')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->primer_apellido),
                        Forms\Components\TextInput::make('segundo_apellido')
                            ->label('Segundo Apellido')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->segundo_apellido),
                        Forms\Components\Select::make('tipo_documento')
                            ->label('Tipo de Documento')
                            ->required()
                            ->options(TipoIdentificacion::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->default($user->tipo_documento_id),
                        Forms\Components\Select::make('ocupacion')
                            ->label('Ocupación')
                            ->required()
                            ->options(Profesion::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->default($user->ocupacion_id),
                        Forms\Components\TextInput::make('direccion')
                            ->label('Dirección')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->direccion),
                        Forms\Components\Select::make('barrio')
                            ->label('Barrio')
                            ->required()
                            ->options(Barrio::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->default($user->barrio_id),
                        Forms\Components\Select::make('ciudad')
                            ->label('Ciudad')
                            ->required()
                            ->options(Ciudad::all()->pluck('nombre', 'id'))
                            ->searchable()
                            ->default($user->ciudad_id),
                        Forms\Components\TextInput::make('nro_celular_1')
                            ->label('Nro Celular 1')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->nro_celular_1),
                        Forms\Components\TextInput::make('nro_telefono_fijo')
                            ->label('Telefono Fijo')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->nro_telefono_fijo),
                        Forms\Components\TextInput::make('email')
                            ->label('Correo')
                            ->required()
                            ->autocomplete(false)
                            ->default($user->email),
                    ])->columns(3),
                Section::make('Datos Financieros')
                    ->description('Aqui debes actualizar los datos financieros, de lo contrario no se modifica nada')
                    ->icon('heroicon-m-wallet')
                    ->schema([
                        Forms\Components\TextInput::make('total_activos')->label('Total Activos')->mask('9999999,99')->autocomplete(false),
                        Forms\Components\TextInput::make('total_pasivos')->label('Total Pasivos')->mask('9999999,99')->autocomplete(false),
                        Forms\Components\TextInput::make('salario')->label('Salario')->mask('9999999.99')->autocomplete(false),
                        Forms\Components\TextInput::make('servicios')->label('Servicios')->mask('9999999,99')->autocomplete(false),
                        Forms\Components\TextInput::make('gastos_financieros')->label('Gastos Financieros')->mask('9999999,99')->autocomplete(false),
                        Forms\Components\TextInput::make('arriendos')->label('Arriendos / Cuota Vivienda')->mask('9999999,99')->autocomplete(false),
                        Forms\Components\TextInput::make('otros_gastos')->label('Otros Gastos')->mask('9999999,99')->autocomplete(false),
                    ])->columns(3)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
            ])
            ->filters([
                //
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

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            //'index' => Pages\ListPerfils::route('/'),
            'create' => Pages\CreatePerfil::route('/create'),
            //'edit' => Pages\EditPerfil::route('/{record}/edit'),
        ];
    }
}
