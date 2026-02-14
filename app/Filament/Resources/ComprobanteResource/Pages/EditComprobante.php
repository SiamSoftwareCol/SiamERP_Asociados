<?php

namespace App\Filament\Resources\ComprobanteResource\Pages;

use App\Exports\ComprobanteExport;
use App\Exports\ComprobanteLineasExport;
use App\Filament\Resources\ComprobanteResource;
use App\Imports\ComprobanteLineaImport;
use App\Models\Comprobante;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\EditRecord;
use App\Models\TipoDocumentoContable;
use App\Models\Puc;
use App\Models\Tercero;
use App\Models\TipoContribuyente;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Icetalker\FilamentTableRepeater\Forms\Components\TableRepeater;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Filament\Support\Colors\Color;
use GuzzleHttp\Psr7\Utils;
use Filament\Support\Enums\ActionSize;

class EditComprobante extends EditRecord
{
    protected static string $resource = ComprobanteResource::class;

    protected static string $view = 'custom.comprobante.edit-comprobante';

    protected function getHeaderActions(): array
    {
        return [


            ActionGroup::make([
                Action::make('Descargar plantilla')
                    ->tooltip('Plantilla para carga masiva de lineas')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color(Color::Blue)
                    ->url(fn(): string => 'https://www.dropbox.com/scl/fi/seidlg604qasmakc2wvno/plantilla.xlsx?rlkey=eb6ddc7jma289rtpp7yynea6e&st=wiyb9sv1&dl=1'),

                Action::make('import_excel')
                    ->label('Importar Lineas')
                    ->color(Color::Blue)
                    ->icon('heroicon-o-document-arrow-up')
                    ->form([
                        FileUpload::make('file_import')
                            ->label('Archivo Excel')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.ms-excel'])
                            ->required(),
                    ])
                    ->action(function (array $data) {

                        try {
                            $filePath = storage_path('app/public/' . $data['file_import']);

                            // Insertamos las lineas
                            Excel::import(new ComprobanteLineaImport($this->getRecord()->id), $filePath);
                            $this->fillForm();

                            Notification::make()
                                ->title('Se importó la información de manera correcta.')
                                ->icon('heroicon-m-check-circle')
                                ->body('Los datos importados correctamente')
                                ->success()
                                ->color('primary')
                                ->send();

                            return;
                        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
                            //dd('Excel import error: ' . $e->getMessage());
                            $failures = $e->failures();

                            if (!$failures) {

                                Notification::make()
                                    ->title('Ocurrió un error!')
                                    ->icon('heroicon-o-exclamation-circle')
                                    ->body('Por favor verifica que los datos estén correctos y que el archivo sea correcto.')
                                    ->seconds(3000)
                                    ->danger()
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->title('Ocurrió un error!')
                                ->icon('heroicon-o-exclamation-circle')
                                ->body(function () use ($failures) {

                                    $errors = [];

                                    foreach ($failures as $failure) {
                                        $message = "Tienes un error en la fila: " . $failure->row() . "<br><br>Error: " . implode(", ", $failure->errors());
                                        array_push($errors, $message);
                                    }

                                    return implode("<br><br>", $errors);
                                })
                                ->seconds(3000)
                                ->danger()
                                ->send();
                        }
                    }),

            ])
                ->label('Opciones de importación')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color(Color::Blue)
                ->button(),



            ActionGroup::make([
                Action::make('export_excel')
                    ->label('Exportar EXCEL')
                    ->color('primary')
                    ->icon('heroicon-c-arrow-down-on-square')
                    ->action(function () {
                        $nameFile = $this->getRecord()->descripcion_comprobante . '.xlsx';
                        return Excel::download(new ComprobanteLineasExport($this->getRecord()->id), $nameFile, \Maatwebsite\Excel\Excel::XLSX);
                    })->after(function () {
                        Notification::make()
                            ->title('Se exporto la información de manera correcta.')
                            ->icon('heroicon-m-check-circle')
                            ->body('Los datos exportados correctamente')
                            ->success()
                            ->color('primary')
                            ->send();
                    }),

                Action::make('export_csv')
                    ->label('Exportar CSV')
                    ->color('primary')
                    ->icon('heroicon-c-arrow-down-on-square')
                    ->action(function () {
                        $nameFile = $this->getRecord()->descripcion_comprobante . '.csv';
                        return Excel::download(new ComprobanteLineasExport($this->getRecord()->id), $nameFile, \Maatwebsite\Excel\Excel::CSV);
                    })->after(function () {
                        Notification::make()
                            ->title('Se exporto la información de manera correcta.')
                            ->icon('heroicon-m-check-circle')
                            ->body('Los datos exportados correctamente')
                            ->success()
                            ->color('primary')
                            ->send();
                    }),

                Action::make('export_pdf')
                    ->label('Exportar PDF')
                    ->color('primary')
                    ->icon('heroicon-c-printer')
                    ->action(function () {
                        return ComprobanteResource::imprimirComprobante($this->getRecord()->id);
                    })
            ])
                ->label('Opciones de exportación')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size(ActionSize::Small)
                ->color('primary')
                ->button(),


            Action::make('volver')
                ->label('Regresar')
                ->icon('heroicon-o-arrow-left')
                ->url(static::getResource()::getUrl('index'))
                ->color('Slate'),
        ];
    }

    public function form(Form $form): Form
    {
        $query = TipoDocumentoContable::all()->toArray();
        $tipoDocumento = [];
        foreach ($query as $row) {
            $tipoDocumento[$row['id']] = "{$row['sigla']} - {$row['tipo_documento']}";
        }
        unset($query);

        $query = Puc::all()->toArray();
        $puc = [];
        foreach ($query as $row) {
            $puc[$row['id']] = "{$row['puc']} - {$row['descripcion']}";
        }
        unset($query);

        $query = TipoContribuyente::all()->toArray();
        $terceroComprobante = [];
        foreach ($query as $row) {
            $terceroComprobante[$row['id']] = $row['nombre'];
        }

        return $form
            ->schema([

                // 🔹 Sección 1: Encabezado del comprobante
                Section::make('Encabezado del Comprobante')
                    ->description('Información principal del comprobante')
                    ->schema([
                        DatePicker::make('fecha_comprobante')
                            ->label('Fecha de comprobante')
                            ->required()
                            ->columnSpan(2)
                            ->native(false)
                            ->disabled(function (Get $get, Set $set): bool {
                                $id = $get('tipo_documento_contables_id');
                                if (!is_null($id)) {
                                    $isDateModified = TipoDocumentoContable::all()->find($id)->toArray()['fecha_modificable'];
                                    if ($isDateModified == 1) {
                                        return false;
                                    } else {
                                        $set('fecha_comprobante', date('Y-m-d'));
                                        return true;
                                    }
                                } else {
                                    return false;
                                }
                            }),

                        Select::make('tipo_documento_contables_id')
                            ->label('Tipo de Documento')
                            ->columnSpan(3)
                            ->options($tipoDocumento)
                            ->required()
                            ->native(false)
                            ->live(),

                        TextInput::make('n_documento')
                            ->label('Nº de Documento')
                            ->columnSpan(1)
                            ->rule('regex:/^[0-9]+$/')
                            ->required(),

                        Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'Activo' => 'Activo',
                                'Inactivo' => 'Inactivo',
                            ])
                            ->default('Activo')
                            ->required()
                            ->native(false)
                            ->columnSpan(2),

                        Select::make('tercero_id')
                            ->label('Tercero Comprobante')
                            ->required()
                            ->columnSpan(3)
                            ->native(false)
                            ->relationship('tercero', 'nombre_completo')
                            ->markAsRequired(false)
                            ->searchable(),

                        Textarea::make('descripcion_comprobante')
                            ->label('Descripcion del Comprobante')
                            ->columnSpan(5)
                            ->required(),
                    ])
                    ->columns(8)
                    ->collapsible(),

                Section::make('Detalle del Comprobante')
                    ->schema([
                        TextInput::make('total_debito')->label('Total Débitos')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('$')
                            ->disabled(function (Get $get, Set $set) {
                                $total = 0;
                                foreach ($get('detalle') as $detalle) {
                                    $total += floatval($detalle['debito']);
                                }
                                $set('total_debito', $total);
                                return true;
                            })
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),

                        TextInput::make('total_credito')->label('Total Créditos')
                            ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                            ->prefix('$')
                            ->disabled(function (Get $get, Set $set) {
                                $total = 0;
                                foreach ($get('detalle') as $detalle) {
                                    $total += floatval($detalle['credito']);
                                }
                                $set('total_credito', $total);
                                return true;
                            })
                            ->columnSpan([
                                'sm' => 2,
                                'xl' => 3,
                                '2xl' => 4,
                            ]),


                        TableRepeater::make('detalle')
                            ->label('Líneas del comprobante')
                            ->relationship('comprobanteLinea', fn($query) => $query->limit(30))
                            ->schema([
                                Select::make('pucs_id')
                                    ->label('Cuenta PUC')
                                    ->options(
                                        Puc::where('movimiento', true)
                                            ->selectRaw("id, CONCAT(puc, ' - ', descripcion) as nombre")
                                            ->pluck('nombre', 'id')
                                    )
                                    ->native(false)
                                    ->searchable()
                                    ->required()
                                    ->columnSpan(2),
                                Select::make('tercero_id')
                                    ->label('Tercero Registro')
                                    ->required()
                                    ->native(false)
                                    ->relationship('tercero', 'nombre_completo')
                                    ->markAsRequired(false)
                                    ->searchable()
                                    ->columnSpan(5),
                                TextInput::make('descripcion_linea')
                                    ->label('Descripción Línea')
                                    ->required()
                                    ->columnSpan(3),

                                TextInput::make('debito')
                                    ->placeholder('Débito')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->inputMode('decimal')
                                    ->live(onBlur: true)
                                    ->prefix('$')
                                    ->columnSpan(1)
                                    ->extraAttributes(['class' => 'text-right font-mono']), // alineado a la derecha

                                TextInput::make('credito')
                                    ->placeholder('Crédito')
                                    ->currencyMask(thousandSeparator: ',', decimalSeparator: '.', precision: 2)
                                    ->inputMode('decimal')
                                    ->live(onBlur: true)
                                    ->prefix('$')
                                    ->columnSpan(1)
                                    ->extraAttributes(['class' => 'text-right font-mono']), // alineado a la derecha
                            ])
                            ->columns(12)
                            ->reorderable()
                            ->cloneable()
                            ->collapsible()
                            ->defaultItems(1)
                            ->maxItems(5000),


                    ])
                    ->columns(8)
                    ->collapsible(),
            ]);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['estado'] == true) $data['estado'] = 'Activo';
        else $data['estado'] = 'Inactivo';
        if (array_key_exists('usar_plantilla', $data)) {
            unset($data['usar_plantilla']);
            unset($data['plantilla']);
        }
        if (!array_key_exists('fecha_comprobante', $data)) {
            $data['fecha_comprobante'] = date('Y-m-d');
            return $data;
        } else {
            return $data;
        }
    }

    protected function beforeSave(): void
    {
        $data = $this->data;
        $credito = array();
        $debito = array();

        foreach ($data['detalle'] as $value) {
            $credito[] = floatval($value['credito']) ?? 0.00;
            $debito[] = floatval($value['debito']) ?? 0.00;
        }

        if ((array_sum($credito) - array_sum($debito)) != 0.0) {
            Notification::make()
                ->title('No puede guardar un comprobante desbalanceado')
                ->danger()
                ->send();
            $this->halt();
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
