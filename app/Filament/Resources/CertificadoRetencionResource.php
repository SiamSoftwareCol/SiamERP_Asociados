<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CertificadoRetencionResource\Pages;
use App\Filament\Resources\CertificadoRetencionResource\RelationManagers;
use App\Models\CertificadoRetencion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use App\Models\Tercero;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificadoRetencionResource extends Resource
{
    protected static ?string $model = Tercero::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string    $navigationGroup = 'Informes de Cumplimiento';
    protected static?string     $modelLabel = 'Certificado de Retencion';
    protected static ?string    $pluralModelLabel = 'Certificados de Retencion';
    protected static ?string    $navigationLabel = 'Certificado de Retencion';
    protected static ?int       $navigationSort = 100;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
                ->heading('Certificacion de Retencion')
                ->description('Este menu permite generar los certificados de ingresos y retencion año 2025')
                ->striped()
                ->defaultPaginationPageOption(5)
                ->defaultSort('updated_at', 'desc')
                ->columns([
                    TextColumn::make('tercero_id')
                        ->searchable()
                        ->label('No. de Identificacion'),
                    TextColumn::make('nombres')
                        ->searchable()
                        ->label('Nombres'),
                    TextColumn::make('primer_apellido')
                        ->searchable()
                        ->label('Primer Apellido'),
                    TextColumn::make('segundo_apellido')
                        ->searchable()
                        ->label('Segundo Apellido'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('Imprimir')
                    ->icon('heroicon-o-printer')
                    ->label('Generar PDF')
                    ->action(function (Tercero $record) {
                        $tercero = $record;
                        $certsaldos = $record->certsaldos;
                        $fecha_emision = now();

                        $pdf = Pdf::loadView('pdf.certificado-retencion ', compact('tercero', 'certsaldos'));

                        return response()->streamDownload(
                            fn () => print($pdf->output()),
                            'certificado_retencion_'.$tercero->tercero_id.'.pdf'
                        );
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListCertificadoRetencions::route('/'),
            'create' => Pages\CreateCertificadoRetencion::route('/create'),
            'edit' => Pages\EditCertificadoRetencion::route('/{record}/edit'),
        ];
    }
}
